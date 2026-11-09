<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What an employee may see in the portal.
 *
 * 'barber' is their own day and nothing else — the behaviour every existing
 * row already has, which is why it is the default and why no existing row is
 * touched. 'manager' is the whole shop, and is how the owner reads the day
 * without logging in as each barber in turn.
 *
 * Deliberately not a permissions table. There are two answers, the portal is
 * read-only, and a column the owner can set from a select is the honest shape
 * for that. If the portal ever gains actions, this is the thing to revisit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('portal_role', ['barber', 'manager'])
                ->default('barber')
                ->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('portal_role');
        });
    }
};
