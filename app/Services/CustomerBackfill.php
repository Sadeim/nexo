<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Support\Phone;
use Illuminate\Support\Facades\DB;

/**
 * One customer per phone number, out of the bookings already in the table.
 *
 * Lives in a class rather than inside the migration so that it can be tested
 * against a known mix of rows. A data migration that nobody can run twice is
 * a data migration nobody can check.
 *
 * On this installation it will find nothing: every booking made before
 * 10 Nov 2026 has a NULL phone, because the form never asked for one. That is
 * not a reason to write it carelessly — it is the code that runs against any
 * imported history, and against every environment that does have numbers.
 */
class CustomerBackfill
{
    /**
     * @return array{bookings: int, with_phone: int, unusable: int, customers: int, linked: int}
     */
    public function run(): array
    {
        $report = [
            'bookings' => Booking::count(),
            'with_phone' => 0,
            'unusable' => 0,
            'customers' => 0,
            'linked' => 0,
        ];

        /*
         * Grouped in memory by normalised phone. The alternative — a GROUP BY
         * in SQL — cannot normalise, so '(224) 669-8063' and '2246698063'
         * would come back as two groups and become two customers, which is
         * the one thing this must not do.
         */
        $groups = [];

        Booking::query()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('id')
            ->select(['id', 'name', 'email', 'phone', 'created_at'])
            ->chunkById(500, function ($chunk) use (&$groups, &$report) {
                foreach ($chunk as $booking) {
                    $report['with_phone']++;

                    $phone = Phone::isPlausible($booking->phone)
                        ? Phone::normalize($booking->phone)
                        : null;

                    // A number too short to dial is not a customer. The
                    // booking keeps whatever was typed and stays unlinked.
                    if ($phone === null) {
                        $report['unusable']++;

                        continue;
                    }

                    $groups[$phone][] = $booking;
                }
            });

        foreach ($groups as $phone => $bookings) {
            // The most recent booking is the best guess at who they are now:
            // people change their surname and their email, and the last time
            // they told us is the time to believe.
            $latest = end($bookings);

            [$first, $last] = $this->splitName($latest->name);

            $customer = Customer::firstOrNew(['phone' => $phone]);

            $customer->fill(array_filter([
                'first_name' => $first,
                'last_name' => $last,
                'email' => $latest->email,
            ], fn ($value) => filled($value)));

            // Never invented here. Nobody who booked before the consent box
            // existed has agreed to anything.
            $customer->sms_marketing_consent = $customer->sms_marketing_consent ?? false;

            $customer->bookings_count = count($bookings);
            $customer->last_booked_at = collect($bookings)->max('created_at');
            $customer->save();

            $report['customers']++;

            // Updated in one statement per customer rather than one per
            // booking: a customer with forty appointments is one write.
            $report['linked'] += DB::table('bookings')
                ->whereIn('id', collect($bookings)->pluck('id'))
                ->update(['customer_id' => $customer->id]);
        }

        return $report;
    }

    /**
     * "Omar Haddad Jr" -> ["Omar", "Haddad Jr"].
     *
     * First token, then everything else. Nothing clever: a surname can be two
     * words, a middle name is not worth a column, and guessing beyond this
     * would get more names wrong than it got right.
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function splitName(?string $name): array
    {
        $name = trim(preg_replace('/\s+/', ' ', (string) $name));

        if ($name === '') {
            return [null, null];
        }

        $parts = explode(' ', $name, 2);

        return [$parts[0], $parts[1] ?? null];
    }
}
