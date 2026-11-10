<?php

use App\Services\CustomerBackfill;
use Illuminate\Database\Migrations\Migration;

/**
 * Turn the bookings already in the table into customers.
 *
 * The work is in App\Services\CustomerBackfill so it can be tested against a
 * known mix of rows; this is the thing that runs it once, during the deploy,
 * while the site is down.
 *
 * On nexobarbers.com it will report zeroes. Every booking made before this
 * migration has a NULL phone — checked on 10 Nov 2026: one distinct value,
 * NULL, across all 1,762 rows — because the form never asked for one. The
 * customer list therefore starts empty and fills from the first booking taken
 * through the new modal onwards.
 */
return new class extends Migration
{
    public function up(): void
    {
        $report = (new CustomerBackfill)->run();

        foreach ($report as $label => $count) {
            // Printed, so the deploy log carries the numbers rather than the
            // bare word DONE. A silent data migration is one nobody checks.
            echo sprintf("    backfill: %-12s %d\n", $label, $count);
        }
    }

    /**
     * Unlinks, and deletes nothing.
     *
     * Rolling back a data migration that created people would mean deciding
     * which of them were created here and which were created since, minutes
     * later, by a customer at the booking form. Unlinking is reversible;
     * deleting a customer who has consented to be texted is not.
     */
    public function down(): void
    {
        \DB::table('bookings')->update(['customer_id' => null]);
    }
};
