<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reason::create([
            'icon' => 'tji-human',
            'title' => 'Full Range of Electrical Services',
            'description' => 'Handyman projects often focus on immediate needs...',
        ]);
    }
}
