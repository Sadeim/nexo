<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::firstOrCreate([
            'email' => 'admin@gmail.com'
        ],[
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            // 'admin_image' => '/admin/media/our-image/logo-colored.png',
            'password' => bcrypt('123456789')
        ]);

        // assign permissions to admin role
        $admin_role = Role::where('name', 'admin')->first();
        $permissions = Permission::where('guard_name', 'admin')->pluck('id','id');
        $admin_role->syncPermissions($permissions);

        // assign role to the admin admin
        $admin->assignRole([$admin_role->id]);
    }
}
