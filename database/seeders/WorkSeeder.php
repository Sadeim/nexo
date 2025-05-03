<?php

namespace Database\Seeders;

use App\Models\Work;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Work::create([
            'title'       => 'Industrial Facility Power Upgrade',
            'slug'        => Str::slug('Industrial Facility Power Upgrade'),
            'image'       => 'frontend_assets/assets/images/resource/process1.png',
            'category'    => 'Electrical',
            'description' => 'Upgrading power systems in an industrial facility for improved efficiency and safety.',
        ]);

        Work::create([
            'title'       => 'Outdoor Lighting for Urban Parks',
            'slug'        => Str::slug('Outdoor Lighting for Urban Parks'),
            'image'       => 'frontend_assets/assets/images/resource/process2.png',
            'category'    => 'Electrical',
            'description' => 'Installing energy-efficient outdoor lighting to beautify urban parks.',
        ]);

        Work::create([
            'title'       => 'Residential Solar Installation',
            'slug'        => Str::slug('Residential Solar Installation'),
            'image'       => 'frontend_assets/assets/images/resource/process3.png',
            'category'    => 'Electrical',
            'description' => 'Solar panel installation for a residential property to save on energy bills.',
        ]);

        Work::create([
            'title'       => 'Revamping Modern Office Lighting',
            'slug'        => Str::slug('Revamping Modern Office Lighting'),
            'image'       => 'frontend_assets/assets/images/resource/process4.png',
            'category'    => 'Electrical',
            'description' => 'Updating office lighting systems to modern, energy-efficient solutions.',
        ]);
    }
}
