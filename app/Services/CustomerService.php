<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Support\Phone;
use Illuminate\Support\Carbon;

/**
 * The one way a customer record is created or changed by the public site.
 *
 * Both halves of the booking modal come through here — step one, which knows
 * only a phone number, and step two, which knows the rest. They must agree on
 * what counts as the same person, or a customer who hesitates between the two
 * steps ends up as two rows.
 */
class CustomerService
{
    /**
     * Find or create the customer behind a phone number, and fold in whatever
     * else we have just been told about them.
     *
     * Only ever widens the record. A missing first name does not erase the
     * one already stored, because step one posts no name at all and would
     * otherwise wipe the name of every returning customer the moment they
     * typed their number. Supplied values do win over stored ones: somebody
     * correcting their own spelling is the whole reason to accept them.
     *
     * Returns null for anything that is not a usable number, so a caller
     * cannot create a customer out of a typo.
     */
    public function upsertByPhone(?string $rawPhone, array $details = []): ?Customer
    {
        if (! Phone::isPlausible($rawPhone)) {
            return null;
        }

        $phone = Phone::normalize($rawPhone);

        $customer = Customer::firstOrNew(['phone' => $phone]);

        foreach (['first_name', 'last_name', 'email'] as $field) {
            if (array_key_exists($field, $details) && filled($details[$field])) {
                $customer->{$field} = $details[$field];
            }
        }

        /*
         * Consent is only ever granted here, never withdrawn.
         *
         * An unticked box on a later booking means "this form did not ask me
         * again", not "I take it back" — treating it as a withdrawal would
         * quietly empty the list every time a consenting customer rebooked.
         * Withdrawal is an opt-out, and it does not come through a booking
         * form; the timestamp and the source are kept so the first grant can
         * always be pointed at.
         */
        if (! empty($details['sms_marketing_consent']) && ! $customer->sms_marketing_consent) {
            $customer->sms_marketing_consent = true;
            $customer->sms_consent_at = Carbon::now();
            $customer->consent_source = $details['consent_source'] ?? 'booking_modal';
        }

        $customer->save();

        return $customer;
    }

    /**
     * Attach a booking that has just been made, and refresh the totals.
     *
     * bookings_count is counted rather than incremented: an increment is
     * wrong the moment anything else links a booking, and counting 1,700 rows
     * for one customer is a single indexed query.
     */
    public function attachBooking(Customer $customer, Booking $booking): void
    {
        $booking->customer_id = $customer->id;
        $booking->save();

        $this->refreshTotals($customer);
    }

    /**
     * bookings_count and last_booked_at, from the bookings themselves.
     *
     * last_booked_at is when the booking was MADE, not when the appointment
     * is. A booking placed today for next week says the customer was in touch
     * today; that is the fact a "we have not heard from them in months" list
     * is built on, and it is also the only one that cannot be in the future.
     */
    public function refreshTotals(Customer $customer): void
    {
        $bookings = Booking::where('customer_id', $customer->id);

        $customer->forceFill([
            'bookings_count' => (clone $bookings)->count(),
            'last_booked_at' => (clone $bookings)->max('created_at'),
            'last_visit_at' => $this->lastVisitFor($customer->id),
        ])->save();
    }

    /**
     * When this customer was last in the chair, from their own bookings.
     *
     * The most recent appointment that has already happened and was not
     * cancelled. "Already happened" is measured against the appointment's own
     * date AND time rather than the date alone, so a haircut at ten this
     * morning counts this afternoon rather than tomorrow — and a booking made
     * today for next week never counts, which is the entire reason this
     * column exists alongside last_booked_at.
     *
     * Returns null for somebody who has only ever booked ahead. That is a
     * real answer, not a missing one: they have not been in yet.
     */
    public function lastVisitFor(int $customerId): ?Carbon
    {
        $now = Carbon::now();

        $booking = Booking::query()
            ->where('customer_id', $customerId)
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            /*
             * Half-open, and NOT `<= today`.
             *
             * The `date` cast writes a full datetime, so the column holds
             * '2026-11-10 00:00:00'. Compared against the string '2026-11-10'
             * that is GREATER, and today's appointments vanish. The exact cut
             * is made below anyway, where the time of day is available; this
             * is only here to keep the result set small.
             */
            ->where('date', '<', $now->copy()->addDay()->toDateString())
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get(['date', 'time'])
            ->map(fn ($row) => Carbon::parse(
                Carbon::parse($row->date)->format('Y-m-d').' '.$row->time
            ))
            ->filter(fn (Carbon $at) => $at->lessThanOrEqualTo($now))
            ->sortDesc()
            ->first();

        return $booking;
    }

    /**
     * The nightly sweep: every customer's last_visit_at, brought up to date.
     *
     * Needed because the value goes stale on its own. Nothing happens when an
     * appointment arrives — the booking was written weeks earlier — so
     * without a sweep a customer's last visit would only ever update on the
     * day they booked again, which is precisely the customer this column is
     * meant to find.
     *
     * One pass over the bookings rather than one query per customer: a shop
     * with five thousand customers would otherwise run five thousand queries
     * at three in the morning on a box that is already busy.
     *
     * @return int how many customers changed
     */
    public function refreshAllVisits(): int
    {
        $now = Carbon::now();
        $latest = [];

        Booking::query()
            ->whereNotNull('customer_id')
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            // Half-open, for the reason spelled out in lastVisitFor().
            ->where('date', '<', $now->copy()->addDay()->toDateString())
            ->orderBy('id')
            ->select(['id', 'customer_id', 'date', 'time'])
            ->chunkById(1000, function ($chunk) use (&$latest, $now) {
                foreach ($chunk as $booking) {
                    $at = Carbon::parse(
                        Carbon::parse($booking->date)->format('Y-m-d').' '.$booking->time
                    );

                    if ($at->greaterThan($now)) {
                        continue;
                    }

                    $id = $booking->customer_id;

                    if (! isset($latest[$id]) || $at->greaterThan($latest[$id])) {
                        $latest[$id] = $at;
                    }
                }
            });

        $changed = 0;

        Customer::query()->orderBy('id')->chunkById(500, function ($chunk) use ($latest, &$changed) {
            foreach ($chunk as $customer) {
                $value = $latest[$customer->id] ?? null;

                // Compared as strings so an unchanged customer is not written
                // every night just to set the same timestamp again.
                $before = optional($customer->last_visit_at)->toDateTimeString();
                $after = optional($value)->toDateTimeString();

                if ($before === $after) {
                    continue;
                }

                $customer->forceFill(['last_visit_at' => $value])->save();
                $changed++;
            }
        });

        return $changed;
    }
}
