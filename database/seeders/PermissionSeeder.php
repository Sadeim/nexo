<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // make admin pages permissions
        $admin_parents = [
            // 'admins' => ['view', 'add', 'edit', 'delete'],
            // 'users' => ['view', 'add', 'edit', 'delete'],
            'products' => ['view', 'add', 'edit', 'delete'],
            'categories' => ['view', 'add', 'edit', 'delete'],
            'static_pages' => ['view', 'add', 'edit', 'delete'],
            'newsletters' => ['view', 'add', 'edit', 'delete'],
            'services' => ['view', 'add', 'edit', 'delete'],
            'testimonials' => ['view', 'add', 'edit', 'delete'],
            'achievements' => ['view', 'add', 'edit', 'delete'],
            'abouts' => ['view', 'add', 'edit', 'delete'],
            // 'banners' => ['view', 'add', 'edit', 'delete'],
            'blogs' => ['view', 'add', 'edit', 'delete'],
            'clients' => ['view', 'add', 'edit', 'delete'],
            'bookings' => ['view', 'add', 'edit', 'delete'],
            'contacts' => ['view', 'add', 'edit', 'delete'],
            'events' => ['view', 'add', 'edit', 'delete'],
            'features' => ['view', 'add', 'edit', 'delete'],
            'instagrams' => ['view', 'add', 'edit', 'delete'],
            'menu_items' => ['view', 'add', 'edit', 'delete'],
            'sliders' => ['view', 'add', 'edit', 'delete'],
            'faqs' => ['view', 'add', 'edit', 'delete'],
            'reasons' => ['view', 'add', 'edit', 'delete'],
            'teams' => ['view', 'add', 'edit', 'delete'],
            'skills' => ['view', 'add', 'edit', 'delete'],
            'works' => ['view', 'add', 'edit', 'delete'],
            'home' => ['view'],
            'settings' => ['view', 'add', 'edit', 'delete'],
            'how' => ['view', 'add', 'edit', 'delete'],
            'approaches' => ['view', 'add', 'edit', 'delete'],
            'contacts' => ['view', 'add', 'edit', 'delete'],
        ];
        foreach ($admin_parents as $parent => $types) {
            foreach ($types as $type) {
                Permission::create(['name_key' => $type, 'guard_name'=>'admin', 'name' => "$type" . "_" . $parent, 'parent' => $parent]);
            }
        }
    }
}
