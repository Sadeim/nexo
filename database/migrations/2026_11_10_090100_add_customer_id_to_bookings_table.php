<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which customer a booking belongs to.
 *
 * Nullable, and it stays nullable: every one of the 1,762 bookings that
 * existed on 10 Nov 2026 has no phone at all, so none of them can be matched
 * to anybody. They remain exactly what they were.
 *
 * name, email and phone stay on the booking too. They are what the customer
 * typed that day, and a record of an appointment should not change because
 * somebody later corrected the spelling of their name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('id')
                ->constrained('customers')
                // Deleting a customer must not delete the shop's record of
                // the work it did. The booking survives, unlinked.
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
