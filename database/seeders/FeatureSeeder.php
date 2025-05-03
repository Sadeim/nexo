<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Feature::create([
            'title' => 'Home Renovation',
            'icon' => 'bi-house-gear',
            'image' => 'frontend_assets/assets/images/resource/feature.jpg',
            'link' => '#',
        ]);

        Feature::create([
            'title' => 'Commercial Repairing',
            'icon' => 'bi-house-gear',
            'image' => 'frontend_assets/assets/images/resource/feature.jpg',
            'link' => '#',
        ]);

        Feature::create([
            'title' => 'exterior Renovation',
            'icon' => 'bi-house-gear',
            'image' => 'frontend_assets/assets/images/resource/feature.jpg',
            'link' => '#',
        ]);
    }
}
