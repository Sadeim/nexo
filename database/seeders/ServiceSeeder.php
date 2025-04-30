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
            'name' => 'Web Development',
            'icon' => 'code',
            'description' => 'We build professional websites using the latest technologies.',
            'image' => 'web_development.jpg', 
            'is_featured' => 1,
        ]);

        Service::create([
            'name' => 'Graphic Design',
            'icon' => 'brush',
            'description' => 'We offer creative and modern graphic designs tailored to your needs.',
            'image' => 'service.png', 
            'is_featured' => 1,
        ]);
    }
}
