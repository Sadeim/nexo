<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Portal credentials for barbers.
 *
 * Employees were a label on a sale, never an account. These four columns turn
 * one into something that can sign in, without disturbing a single existing
 * row: every barber already on file keeps working exactly as before and simply
 * has no portal until the owner gives them a phone and a password.
 *
 * phone and password are both nullable on purpose. "Portal not set up" is the
 * default state and has to be representable; an employee with no phone cannot
 * be looked up at login and an employee with no password cannot authenticate,
 * so an unconfigured row is unreachable rather than open.
 *
 * must_change_password defaults to true so the owner-typed temporary password
 * is always a one-time credential, including for rows that predate this.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Unique so a phone number identifies exactly one barber at login.
            // Nullable, and both MySQL and SQLite allow repeated NULLs in a
            // unique index, so any number of barbers may have no phone at all.
            $table->string('phone', 32)->nullable()->unique()->after('slug');

            $table->string('password')->nullable()->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('password');
            $table->boolean('must_change_password')->default(true)->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // The index goes before the column it covers.
            $table->dropUnique('employees_phone_unique');
            $table->dropColumn(['phone', 'password', 'last_login_at', 'must_change_password']);
        });
    }
};
