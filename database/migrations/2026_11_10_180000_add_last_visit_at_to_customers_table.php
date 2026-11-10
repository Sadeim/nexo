<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * When this customer was last actually in the chair.
 *
 * Distinct from last_booked_at, which is when they last PLACED a booking.
 * Somebody who books three weeks ahead has a recent last_booked_at and may
 * not have been seen since the summer; somebody who walks in every fortnight
 * and books on the day has the two within minutes of each other. "Who have we
 * not seen in a while" is the first question an SMS list gets asked, and
 * last_booked_at is the wrong answer to it.
 *
 * Both are kept, because they answer different questions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('last_visit_at')->nullable()->after('last_booked_at');

            // The column exists to be filtered and sorted on.
            $table->index('last_visit_at');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['last_visit_at']);
            $table->dropColumn('last_visit_at');
        });
    }
};
