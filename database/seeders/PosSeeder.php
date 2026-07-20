<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Sets up POS access control on top of the EXISTING `admin` guard:
 *   - a single `pos.access` permission
 *   - a single `cashier` role (all cashiers are equal)
 *   - a demo cashier account so the team can log in immediately
 *
 * Idempotent: safe to run repeatedly.
 */
class PosSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Permission (uses this project's custom columns: name_key, parent).
        $permission = Permission::firstOrCreate(
            ['name' => 'pos.access', 'guard_name' => 'admin'],
            ['name_key' => 'access', 'parent' => 'pos']
        );

        // 2) Cashier role on the admin guard.
        $cashier = Role::firstOrCreate([
            'name' => 'cashier',
            'guard_name' => 'admin',
        ]);
        $cashier->givePermissionTo($permission);

        // 3) The existing super-admin role should also be able to open the POS.
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        // 4) Demo cashier account (username login).
        $demo = Admin::firstOrCreate(
            ['username' => 'PosAdmin'],
            [
                'name'     => 'Demo Cashier',
                'email'    => 'posadmin@gmail.com',
                'password' => Hash::make('123456789'),
                'status'   => 1,
            ]
        );

        if (!$demo->hasRole('cashier')) {
            $demo->assignRole($cashier);
        }
    }
}
