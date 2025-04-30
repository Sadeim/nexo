<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            ['title' => 'Happy Clients', 'description' => 'Celebrated 365 consecutive days without a safety incident on any job site. This accomplishment reflects our unwavering dedication to maintaining the highest safety standards for our team.', 'year' => '2020'],
            ['title' => 'Projects Done', 'description' => 'Successfully completed 15 major projects, showcasing our ability to deliver high-quality construction services on time and within budget. These projects ranged from innovative commercial modern.', 'year' => '2022'],
            ['title' => 'Awards', 'description' => 'Celebrated 365 consecutive days without a safety incident on any job site. This accomplishment reflects our unwavering dedication to maintaining the highest safety standards for our team.', 'year' => '2023'],
            ['title' => 'Team Members', 'description' => 'Celebrated 365 consecutive days without a safety incident on any job site. This accomplishment reflects our unwavering dedication to maintaining the highest safety standards for our team.', 'year' => '2025'],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
