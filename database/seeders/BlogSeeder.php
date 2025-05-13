<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Blog::create([
            'title' => 'The Importance of Digital Marketing',
            'slug' => 'digital-marketing',
            'image' => 'frontend_assets/images/post-1.jpg',
            'category' => 'Marketing',
            'author' => 'Admin',
            'content' => 'An article discussing the importance of digital marketing in today’s business world.',
        ]);

        Blog::create([
            'title' => 'Top Growth Strategies',
            'slug' => 'growth-strategies',
            'image' => 'frontend_assets/images/post-1.jpg',
            'category' => 'Business',
            'author' => 'Ahmed',
            'content' => 'This post outlines the best strategies to grow your business efficiently.',
        ]);
    }
}
