<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'name' => 'Home Renovation',
            'description' => 'Complete renovation and remodeling for your home with modern solutions.',
            'icon' => 'bi-house-gear',
            'image' => 'frontend_assets/images/icon-service-1.svg',
            'is_featured' => true,
        ]);

        Service::create([
            'name' => 'Commercial Repairing',
            'description' => 'Repurpose go forward benefits without goal conveniently targeted to business',
            'image' => 'frontend_assets/images/icon-service-2.svg',
            'icon' => 'service-icn1.png',
            'is_featured' => true,
        ]);

        Service::create([
            'name' => 'Commercial Repairing',
            'description' => 'Repurpose go forward benefits without goal conveniently targeted to business',
            'image' => 'frontend_assets/images/icon-service-3.svg',
            'icon' => 'service-icn1.png',
            'is_featured' => true,
        ]);

        Service::create([
            'name' => 'Commercial Repairing',
            'description' => 'Repurpose go forward benefits without goal conveniently targeted to business',
            'image' => 'frontend_assets/images/icon-service-4.svg',
            'icon' => 'service-icn1.png',
            'is_featured' => true,
        ]);
    }
}
