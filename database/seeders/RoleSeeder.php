<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create roles same as user types
        foreach (Admin::USER_TYPES as $user_type) {
            if ($user_type !== 'admin') {
                Role::create(['name' => $user_type, 'guard_name' => 'admin']);
            }
            Role::create(['name' => $user_type, 'guard_name' => $user_type]);
        }
    }
}
