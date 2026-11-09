<?php

namespace Tests\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * The roles and permissions the access-control tests assume exist.
 *
 * These tests used to rely on a developer's MySQL database already carrying
 * the output of PosSeeder, which made them unrunnable on a fresh checkout and
 * impossible to run on an in-memory database. Creating the two roles here
 * costs a few rows and makes the suite self-sufficient.
 *
 * Built directly rather than by calling the seeders, which would also pull in
 * the whole CMS fixture set and a demo login this has no use for.
 */
trait SeedsAccessControl
{
    protected function seedAccessControl(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $posAccess = Permission::firstOrCreate(
            ['name' => 'pos.access', 'guard_name' => 'admin'],
            ['name_key' => 'access', 'parent' => 'pos']
        );

        foreach (['admin', 'cashier'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'admin'])
                ->givePermissionTo($posAccess);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
