<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $static_pages = [
            [
                'id' => 1,
                'static_page_id' => 1,
                'status' => 1,
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => 'Loream ispazom',
            ],
            [
                'id' => 2,
                'static_page_id' => 2,
                'status' => 1,
                'slug' => 'terms-service',
                'title' => 'Terms Service',
                'content' => 'Loream ispazom',
            ],
            [
                'id' => 3,
                'static_page_id' => 3,
                'status' => 1,
                'slug' => 'return-plicy',
                'title' => 'Return plicy',
                'content' => 'Loream ispazom',
            ],
            [
                'id' => 4,
                'static_page_id' => 4,
                'status' => 1,
                'slug' => 'delivery',
                'title' => 'Delivery',
                'content' => 'Loream ispazom',
            ],
        ];

        foreach ($static_pages as $static_page) {
            $this->seedStaticPage($static_page);
        }
    }

    public static function seedStaticPage($static_page) 
    {
            $new_static_page = StaticPage::firstOrCreate([
                'id' => $static_page['id'],
                'title' => $static_page['title'],
                'content' => $static_page['content'],
                'slug' => $static_page['slug'],
                'status' => $static_page['status']
            ]);

        return true;
    }
}
