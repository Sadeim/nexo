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
            'percent' => 96,
            'text'    => 'Technical Knowledge',
        ]);

        Skill::create([
            'percent' => 92,
            'text'    => 'Problem-Solving Skills',
        ]);

        Skill::create([
            'percent' => 94,
            'text'    => 'Attention to Detail',
        ]);
    }
}
