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
            'title' => 'LaExotic',
            'subtitle' => 'Dishes',
            'description' => 'Truly exotic and appetizing cuisine for those special moments in life',
            'image' => 'sliders/slider1.jpg',
            'button_text' => 'our menus',
            'button_link' => '#',
            'order' => 1,
            'is_active' => true
        ]);
    }
}
