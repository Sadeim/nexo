<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::create([
            'name' => 'PeGuy Hawekins',
            'position' => 'Interior Designer',
            'image' => '/frontend_assets/assets/images/resource/team2.png',
            'social_links' => json_encode([
                'facebook' => 'https://www.facebook.com/',
                'instagram' => 'https://www.instagram.com/',
                'linkedin' => 'https://www.linkedin.com/',
                'twitter' => 'https://x.com/'
            ]),
        ]);

        Team::create([
            'name' => 'Dianne Russell',
            'position' => 'Founder & CEO',
            'image' => '/frontend_assets/assets/images/resource/team2.png',
            'social_links' => json_encode([
                'facebook' => 'https://www.facebook.com/Dianne',
                'instagram' => 'https://www.instagram.com/Dianne',
                'linkedin' => 'https://www.linkedin.com/Dianne',
                'twitter' => 'https://x.com/Dianne'
            ]),
        ]);

        Team::create([
            'name' => 'Kristin Watson',
            'position' => 'Project Manager',
            'image' => '/frontend_assets/assets/images/resource/team2.png',
            'social_links' => json_encode([
                'facebook' => 'https://www.facebook.com/Kristin',
                'instagram' => 'https://www.instagram.com/Kristin',
                'linkedin' => 'https://www.linkedin.com/Kristin',
                'twitter' => 'https://x.com/Kristin'
            ]),
        ]);

        Team::create([
            'name' => 'Darlene Robertson',
            'position' => 'Master Carpenter',
            'image' => '/frontend_assets/assets/images/resource/team2.png',
            'social_links' => json_encode([
                'facebook' => 'https://www.facebook.com/Darlene',
                'instagram' => 'https://www.instagram.com/Darlene',
                'linkedin' => 'https://www.linkedin.com/Darlene',
                'twitter' => 'https://x.com/Darlene'
            ]),
        ]);
    }
}
