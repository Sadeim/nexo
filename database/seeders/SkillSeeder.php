<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Skill::create([
            'image' => 'images/skill-1.jpg',
            'image2' => 'images/skill-2.jpg',
            'image3' => 'images/skill-3.jpg',
            'title' => 'Handyman expertise for every task',
            'description' => 'Our skilled professionals bring versatile expertise to handle a wide range of repairs, installations, and improvements, ensuring every task is completed with precision and care.',
            'percent1' => 96,
            'text1' => 'Technical Knowledge',
            'percent2' => 92,
            'text2' => 'Problem-Solving Skills',
            'percent3' => 94,
            'text3' => 'Attention to Detail',
        ]);
    }
}
