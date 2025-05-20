<?php

namespace Database\Seeders;

use App\Models\Approach;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApproachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Approach::create([
            'title' => 'our approach',
            'subtitle' => 'Handyman services with personal touch',
            'image_1' => 'frontend_assets/images/our-approch-img-1.jpg',
            'image_2' => 'frontend_assets/images/our-approch-img-2.jpg',

            'mission_description' => 'Our mission is to provide reliable, high-quality handyman services that enhance homes and simplify lives, delivering craftsmanship with integrity and care.',
            'mission_points' => [
                'dependable repairs, every time',
                'improving homes, enhancing lives',
                'customer-centered approach'
            ],

            'vision_description' => 'Our vision is to provide reliable, high-quality handyman services that enhance homes and simplify lives, delivering craftsmanship with integrity and care.',
            'vision_points' => [
                'dependable repairs, every time',
                'improving homes, enhancing lives',
                'customer-centered approach'
            ],

            'value_description' => 'Our value is to provide reliable, high-quality handyman services that enhance homes and simplify lives, delivering craftsmanship with integrity and care.',
            'value_points' => [
                'dependable repairs, every time',
                'improving homes, enhancing lives',
                'customer-centered approach'
            ],
        ]);
    }
}
