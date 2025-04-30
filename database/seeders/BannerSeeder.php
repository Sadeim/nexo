<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'title' => 'Welcome to Our Website',
            'sub_title' => 'The best digital services at your fingertips',
            'image' => 'banner.jpg',
            'description' => 'Discover the Difference with Electric Services. Your Trusted Local Experts in Electrical Contracting.',
            'button_text' => 'services text',
            'button_link' => '#services',
        ]);
    }
}
