<?php

namespace Database\Seeders;

use App\Models\About;
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
            'title' => 'Around the world, one plate at a time',
            'description' => null,
            'image1' => 'about/about1.jpg',
            'image2' => 'about/about2.jpg',
            'tab1_title' => 'Dance',
            'tab1_content' => '<p>Welcome to <span>La.Revi</span>, one of the best restaurants in the country. This is the place where food meets passion...</p>',
            'tab2_title' => 'Drink',
            'tab2_content' => '<p>Welcome to <span>La.Revi</span>, one of the best restaurants in the country. This is the place where food meets passion...</p>',
            'tab3_title' => 'Enjoy',
            'tab3_content' => '<p>Welcome to <span>La.Revi</span>, one of the best restaurants in the country. This is the place where food meets passion...</p>',
            'button_text' => 'find a table',
            'button_link' => '#'
        ]);
    }
}
