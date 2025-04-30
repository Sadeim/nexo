<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'title' => 'Sunday Karoke Night',
            'description' => 'Join us for a fun night of singing and great food',
            'date' => '2025-12-30',
            'time' => '8:00pm',
            'location' => 'Main Hall',
            'image' => 'events/event1.jpg',
            'is_active' => true
        ]);
    }
}
