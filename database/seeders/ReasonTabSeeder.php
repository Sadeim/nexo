<?php

namespace Database\Seeders;

use App\Models\ReasonTab;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReasonTabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReasonTab::insert([
            [
                'title' => 'Why Choose Us ?',
                'subtitle' => 'Repairing Your <span> House for </span>',
                'image' => 'frontend_assets/assets/images/resource/tab_1.jpg',
                'description' => 'Competently repurpose go forward benefits without goal-oriented ROI...',
                'features' => json_encode([
                    'Repairing Roofing and Door',
                    'Repairing Roofing and Door',
                ]),
                'order' => 1,
            ],
            [
                'title' => 'Our Missions',
                'subtitle' => 'Repairing Your <span> House for </span>',
                'image' => 'assets/images/resource/tab_2.jpg',
                'description' => 'Competently repurpose go forward benefits...',
                'features' => json_encode([
                    'Repairing Roofing and Door',
                    'Repairing Roofing and Door',
                ]),
                'order' => 2,
            ],
            [
                'title' => 'Mission & Vission',
                'subtitle' => 'Repairing Your <span> House for </span>',
                'image' => 'assets/images/resource/tab_3.jpg',
                'description' => 'Competently repurpose go forward benefits...',
                'features' => json_encode([
                    'Repairing Roofing and Door',
                    'Repairing Roofing and Door',
                ]),
                'order' => 3,
            ],
        ]);
    }
}
