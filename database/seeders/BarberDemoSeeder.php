<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Three published barbers and a few priced services, so the public pages have
 * something to render locally.
 *
 * Not part of DatabaseSeeder: these are demo people, and nobody wants them
 * appearing on a production site because someone ran the default seeder.
 * Run it by name:
 *
 *     php artisan db:seed --class=BarberDemoSeeder
 *
 * Idempotent — matches on name, so re-running updates rather than duplicates.
 */
class BarberDemoSeeder extends Seeder
{
    public function run(): void
    {
        $serviceIds = collect([
            ['Skin Fade', 35],
            ['Beard Trim', 20],
            ['Hot Towel Shave', 45],
        ])->map(fn ($s) => Service::firstOrCreate(
            ['name' => $s[0]],
            ['price' => $s[1], 'is_featured' => 0]
        )->id)->all();

        $barbers = [
            ['Omar Haddad',  'Master Barber', 'omarcuts',    2],
            ['Sami Nasser',  'Senior Barber', 'saminasser',  3],
            // Deliberately no photo and no Instagram: this is the one that
            // exercises the initials fallback and the hidden social link.
            ['Khaled Aziz',  'Barber',        null,          2],
        ];

        foreach ($barbers as $i => [$name, $title, $instagram, $specialtyCount]) {
            $employee = Employee::firstOrCreate(['name' => $name], [
                'is_active'       => true,
                'sort_order'      => $i,
                'commission_rate' => 40,
            ]);

            $employee->update([
                'slug'           => Str::slug($name),
                'public_profile' => true,
                'title'          => $title,
                'bio'            => 'Twelve years behind the chair. Known for clean lines '
                                  . 'and a conversation you actually want to have.',
                'instagram'      => $instagram,
                'specialties'    => array_slice($serviceIds, 0, $specialtyCount),
            ]);

            $this->command?->info("Published {$name} at /barbers/{$employee->slug}");
        }
    }
}
