<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * POS employees log in with a `username`. The column already exists on
 * `admins` (nullable, no constraint) but is unused by the current admin
 * login. We add a UNIQUE index so usernames are safe to authenticate against.
 *
 * A partial-unique is not portable on MySQL, so NULLs are allowed by the
 * standard unique index (multiple NULLs are permitted in MySQL). Existing
 * admins keep username = NULL and are unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Defensive: collapse any accidental empty-string usernames to NULL so
        // they don't collide under the new unique index.
        DB::table('admins')->where('username', '')->update(['username' => null]);

        Schema::table('admins', function (Blueprint $table) {
            $table->unique('username', 'admins_username_unique');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropUnique('admins_username_unique');
        });
    }
};
