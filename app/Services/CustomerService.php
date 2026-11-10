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
        ])->save();
    }
}
