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
            'image' => 'team/team-1.webp',
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
            'image' => 'team/team-2.webp',
            'social_links' => json_encode([
                'facebook' => 'https://www.facebook.com/',
                'instagram' => 'https://www.instagram.com/',
                'linkedin' => 'https://www.linkedin.com/',
                'twitter' => 'https://x.com/'
            ]),
        ]);

        Team::create([
            'name' => 'Kristin Watson',
            'position' => 'Project Manager',
            'image' => 'team/team-3.webp',
            'social_links' => json_encode([
                'facebook' => 'https://www.facebook.com/',
                'instagram' => 'https://www.instagram.com/',
                'linkedin' => 'https://www.linkedin.com/',
                'twitter' => 'https://x.com/'
            ]),
        ]);

        Team::create([
            'name' => 'Darlene Robertson',
            'position' => 'Master Carpenter',
            'image' => 'team/team-4.webp',
            'social_links' => json_encode([
                'facebook' => 'https://www.facebook.com/',
                'instagram' => 'https://www.instagram.com/',
                'linkedin' => 'https://www.linkedin.com/',
                'twitter' => 'https://x.com/'
            ]),
        ]);
    }
}
