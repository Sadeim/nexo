<?php

namespace Database\Seeders;

use App\Models\Instagram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstagramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Instagram::create([
            'image' => 'instagram/insta1.jpg',
            'link' => 'https://instagram.com',
            'is_active' => true
        ]);
    }
}
