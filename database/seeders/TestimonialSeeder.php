<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimonial::create([
            'name' => 'John Smith',
            'position' => 'Company Director',
            'photo' => 'john.jpg',
            'rating' => 5,
            'message' => 'Excellent service and great support. Highly recommended!',
        ]);

        Testimonial::create([
            'name' => 'Emily Johnson',
            'position' => 'Digital Marketer',
            'photo' => 'emily.jpg',
            'rating' => 5,
            'message' => 'Very professional design and quick delivery. Thank you team!',
        ]);
    }
}
