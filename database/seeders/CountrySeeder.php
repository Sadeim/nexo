<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $countries = [
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'Eygpt', 'code' => 'EG'],
            ['name' => 'United Emarates', 'code' => 'AE'],
            ['name' => 'Suadi Arabia', 'code' => 'SA'],
        ];

        DB::table('countries')->insert($countries);
    }
}
