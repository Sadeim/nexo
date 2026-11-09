<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a booking name the barber the customer asked for.
 *
 * NULL means "any available barber" — which is what every booking made before
 * this migration meant, so existing rows are already correct and need no
 * backfill.
 *
 * No unique index on (date, time, barber_id), deliberately. It looks like the
 * obvious hard guarantee against double-booking one barber, but cancelled
 * bookings are kept rather than deleted, so the cancelled row would keep
 * owning the tuple and a customer could never rebook a slot they had just
 * released. Availability is enforced in BookingSlotService instead, under a
 * transaction with lockForUpdate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('barber_id')
                ->nullable()
                ->after('service_id')
                ->constrained('employees')
                ->nullOnDelete();

            // Availability is always read one day at a time, then narrowed to
            // a slot. Without this, every slot lookup is a full scan.
            $table->index(['date', 'time'], 'bookings_date_time_index');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_date_time_index');
            $table->dropForeign(['barber_id']);
            $table->dropColumn('barber_id');
        });
    }
};
