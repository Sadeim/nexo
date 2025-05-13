<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slider::create([
            'title' => 'Heighst Quality',
            'subtitle' => '100% Satisfaction Guarantee',
            'description' => 'Truly exotic and appetizing cuisine for those special moments in life',
            'image' => 'frontend_assets/images/hero-bg.jpg',
            'button_text' => 'Get An Estimate',
            'button_link' => '/about',
            'order' => 1,
            'is_active' => true
        ]);
    }
}
