<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Gives an employee a public face.
 *
 * Until now `employees` was purely internal — a label the cashier attaches a
 * sale to. These columns let the same row be published as a barber profile on
 * the website, so the person who performs the service, the person a customer
 * books, and the person the payroll report pays are one record rather than
 * three.
 *
 * Every column is nullable or defaulted, and `public_profile` defaults to
 * false, so no existing employee changes behaviour or becomes visible.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->boolean('public_profile')->default(false)->after('is_active');
            $table->string('photo_path')->nullable()->after('avatar');
            $table->string('title')->nullable()->after('photo_path');
            $table->text('bio')->nullable()->after('title');
            $table->string('instagram')->nullable()->after('bio');
            // Service ids from the marketing `services` table — what this
            // barber is known for. Display only; it prices nothing.
            $table->json('specialties')->nullable()->after('instagram');

            // Every public read filters on both flags together.
            $table->index(['public_profile', 'is_active'], 'employees_public_active_index');
        });

        // Backfill a slug for every existing row. The column is nullable, but a
        // published barber without one is a guaranteed 404, and the owner turns
        // publishing on from a list where the slug field is easy to miss.
        // Deterministic and additive: nothing else about the row is touched.
        foreach (DB::table('employees')->select('id', 'name')->orderBy('id')->get() as $employee) {
            $base = Str::slug($employee->name) ?: 'barber';
            $slug = $base;
            $n    = 1;

            while (DB::table('employees')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . (++$n);
            }

            DB::table('employees')->where('id', $employee->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_public_active_index');
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug',
                'public_profile',
                'photo_path',
                'title',
                'bio',
                'instagram',
                'specialties',
            ]);
        });
    }
};
