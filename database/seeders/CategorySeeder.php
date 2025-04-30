<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'id' => 1,
                'category_id' => 1,
                'slug' => 'test1',
                'status' => 1,
                'name' => 'Mixed drinks',
                'description' => 'Loream ispazom',
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'slug' => 'test2',
                // 'level' => 1,
                'status' => 1,
                'name' => 'Test 2',
                'description' => 'Loream ispazom',
            ],
            [
                'id' => 3,
                'category_id' => 3,
                'slug' => 'test3',
                // 'level' => 1,
                'status' => 1,
                'name' => 'Test 3',
                'description' => 'Loream ispazom',
            ],
        ];

        foreach ($categories as $category) {
            $this->seedCategory($category);
        }
    }

    public static function seedCategory($category) 
    {
        Category::firstOrCreate([
            'id' => $category['id'],
            'name' => $category['name'],
            'description' => $category['description'],
            'slug' => $category['slug'],
            'status' => $category['status']
        ]);
        
        return true;
    }
}
