<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MenuItem::create([
            'category_id' => 1,
            'name' => 'Beef burger meal',
            'description' => 'Grilled lamb cutlets, pomegranate glaze, butternut squash',
            'price' => 12.00,
            'image' => 'menu/menu1.jpg',
            'is_featured' => true
        ]);
    }
}
