<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\OpeningHour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::create([
            'title' => 'Repairing Your House for Looks as a New Home',
            'description' => 'Competently repurpose go forward benefits without goal-oriented ROI the conveniently target business opportunities whereas proactive',
            'image1' => 'frontend_assets/images/about-img-1.jpg',
            'image2' => 'frontend_assets/images/about-img-2.jpg',
            'tab1_title' => 'Smart Repair System',
            'tab1_content' => 'Conveniently target business opportunities market-driven solutions',
            'tab2_title' => 'Repairing Roofing and Door',
            'tab2_content' => 'List item one',
            // 'tab3_title' => 'Repairing Roofing and Door',
            // 'tab3_content' => 'List item two',
            'button_text' => 'Call Us',
            'button_link' => 'about.html',
        ]);

        OpeningHour::insert([
            ['about_id' => 1, 'day' => 'Monday', 'from' => '09:30', 'to' => '07:30', 'is_closed' => false],
            ['about_id' => 1, 'day' => 'Tuesday', 'from' => '09:30', 'to' => '07:30', 'is_closed' => false],
            ['about_id' => 1, 'day' => 'Wednesday', 'from' => '09:30', 'to' => '07:30', 'is_closed' => false],
            ['about_id' => 1, 'day' => 'Thursday', 'from' => '09:30', 'to' => '07:30', 'is_closed' => false],
            ['about_id' => 1, 'day' => 'Friday', 'from' => '09:30', 'to' => '07:30', 'is_closed' => false],
            ['about_id' => 1, 'day' => 'Saturday', 'from' => '09:30', 'to' => '07:30', 'is_closed' => false],
            ['about_id' => 1, 'day' => 'Sunday', 'from' => null, 'to' => null, 'is_closed' => true],
        ]);
        
    }
}
