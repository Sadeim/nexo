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
            'icon' => 'bi-house-gear', // يمكن تستخدم bootstrap icon
            'image' => 'service1.jpg',
            'is_featured' => true,
        ]);

        Service::create([
            'name' => 'Commercial Repairing',
            'description' => 'Repurpose go forward benefits without goal conveniently targeted to business',
            'image' => 'service1.jpg',
            'icon' => 'service-icn1.png',
            'is_featured' => true,
        ]);
    }
}
