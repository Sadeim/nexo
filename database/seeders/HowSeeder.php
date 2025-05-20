<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\How;

class HowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        How::create([
            'name' => 'How it works',
            'description' => 'From repairs to home improvements, our comprehensive handyman services cover everything you',
            'image' => 'images/how-it-works.jpg',
           'tap1_name' => 'Tap to call',
           'tap1_number' => '12',
           'tap1_content' => 'Tap to call',

           'tap2_name' => 'Tap to call',
           'tap2_number' => '79',
           'tap2_content' => 'Tap to call',

           'tap3_name' => 'Tap to call',
           'tap3_number' => '68',
           'tap3_content' => 'Tap to call',

           'tap4_name' => 'Tap to call',
           'tap4_number' => '89',
           'tap4_content' => 'Tap to call',
        ]);
    }
}
