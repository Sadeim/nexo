<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::create([
            'question' => 'How do I use the website?',
            'answer' => 'Simply visit the website and follow the instructions on the homepage.',
        ]);

        Faq::create([
            'question' => 'Can I request a refund?',
            'answer' => 'Yes, we offer a 14-day money-back guarantee from the date of purchase.',
        ]);
    }
}
