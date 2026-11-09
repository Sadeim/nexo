<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A database-enforced guarantee that one barber cannot be booked twice at the
 * same time.
 *
 * Availability is decided in BookingSlotService, but a rule evaluated in PHP
 * is a read followed by a write, and two requests can both pass the read.
 * A transaction with lockForUpdate narrows that window and InnoDB's gap locks
 * usually close it under REPEATABLE READ — "usually" and "under" being the
 * problem. This column turns the per-barber half of the rule into a
 * constraint the database cannot be talked out of.
 *
 * slot_key is "{Y-m-d}|{H:i:s}|{barber_id}" for a live booking that names a
 * barber, and NULL otherwise. NULLs never collide in a unique index, which
 * gives exactly the semantics wanted:
 *
 *   - cancelling sets the key back to NULL, so the slot is free immediately
 *     rather than being owned forever by a dead row;
 *   - an "any available barber" booking has no key at all, so the shop's
 *     capacity governs it and nothing else.
 *
 * The value is owned by BookingObserver. Nothing else may write it: a
 * hand-set key is a silently broken constraint.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('slot_key')->nullable()->after('barber_id');
            $table->unique('slot_key', 'bookings_slot_key_unique');
        });

        // Backfill. No row can qualify today — barber_id was added empty one
        // migration ago — but the backfill is written anyway so that replaying
        // this migration against a database that already has assigned
        // bookings produces a correct index rather than an empty one.
        DB::table('bookings')
            ->whereNotNull('barber_id')
            ->where('status', '!=', 'cancelled')
            ->orderBy('id')
            ->select('id', 'date', 'time', 'barber_id')
            ->get()
            ->each(function ($booking) {
                $key = sprintf(
                    '%s|%s|%d',
                    Carbon\Carbon::parse($booking->date)->toDateString(),
                    Carbon\Carbon::parse($booking->time)->format('H:i:s'),
                    $booking->barber_id
                );

                DB::table('bookings')->where('id', $booking->id)->update(['slot_key' => $key]);
            });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_slot_key_unique');
            $table->dropColumn('slot_key');
        });
    }
};
