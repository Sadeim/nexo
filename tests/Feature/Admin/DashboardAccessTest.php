<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Dashboard access-control and user-management safety rules.
 *
 * DatabaseTransactions => runs on the real schema/seeded roles, rolled back
 * after each test (nothing persists in the dev DB). Relies on the seeded
 * `admin` and `cashier` roles (guard: admin).
 */
class DashboardAccessTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAccount(string $role, int $status = 1): Admin
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name'     => ucfirst($role) . ' ' . uniqid(),
            'email'    => $role . '_' . uniqid() . '@nexo.local',
            'password' => Hash::make('Password123'),
            'status'   => $status,
        ]);
        $roleModel = Role::where('name', $role)->where('guard_name', 'admin')->firstOrFail();
        $admin->assignRole($roleModel);

        return $admin;
    }

    /* ---------------- Access control (#2) ---------------- */

    public function test_guest_is_redirected_away_from_dashboard(): void
    {
        $this->get(route('admin.home'))->assertRedirect();
    }

    public function test_cashier_is_forbidden_from_dashboard_home(): void
    {
        $cashier = $this->makeAccount('cashier');
        $this->actingAs($cashier, 'admin')
            ->get(route('admin.home'))
            ->assertForbidden(); // 403
    }

    public function test_cashier_is_forbidden_from_user_management(): void
    {
        $cashier = $this->makeAccount('cashier');
        $this->actingAs($cashier, 'admin')
            ->get(route('admin.accounts.index'))
            ->assertForbidden();
    }

    public function test_cashier_is_forbidden_from_transactions(): void
    {
        $cashier = $this->makeAccount('cashier');
        $this->actingAs($cashier, 'admin')
            ->get(route('admin.pos_transactions.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_dashboard_and_user_management(): void
    {
        $admin = $this->makeAccount('admin');
        $this->actingAs($admin, 'admin')->get(route('admin.accounts.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.pos_transactions.index'))->assertOk();
    }

    /* ---------------- User-management safety (#1) ---------------- */

    public function test_admin_cannot_delete_their_own_account(): void
    {
        // Two admins so the target is NOT the last admin — isolates the self rule.
        $actor = $this->makeAccount('admin');
        $this->makeAccount('admin');

        $this->actingAs($actor, 'admin')
            ->delete(route('admin.accounts.destroy', $actor->id))
            ->assertStatus(403);

        $this->assertDatabaseHas('admins', ['id' => $actor->id]); // still there
    }

    public function test_admin_cannot_disable_their_own_account(): void
    {
        $actor = $this->makeAccount('admin');
        $this->makeAccount('admin');

        $this->actingAs($actor, 'admin')
            ->post(route('admin.accounts.active', $actor->id))
            ->assertStatus(403);

        $this->assertDatabaseHas('admins', ['id' => $actor->id, 'status' => 1]); // still active
    }

    /**
     * Make $target the ONLY active admin in the whole system (rolled back after
     * the test), so the last-active-admin guard can be exercised in isolation.
     */
    private function makeSoleActiveAdmin(): array
    {
        $target = $this->makeAccount('admin', status: 1);
        $actor  = $this->makeAccount('admin', status: 1);
        // Every other admin (incl. seeded Super Admin) becomes inactive.
        Admin::where('id', '!=', $target->id)->update(['status' => 0]);

        return [$target, $actor];
    }

    public function test_cannot_delete_the_last_active_admin(): void
    {
        [$target, $actor] = $this->makeSoleActiveAdmin();

        $this->actingAs($actor, 'admin')
            ->delete(route('admin.accounts.destroy', $target->id))
            ->assertStatus(403);

        $this->assertDatabaseHas('admins', ['id' => $target->id]);
    }

    public function test_cannot_disable_the_last_active_admin(): void
    {
        [$target, $actor] = $this->makeSoleActiveAdmin();

        $this->actingAs($actor, 'admin')
            ->post(route('admin.accounts.active', $target->id))
            ->assertStatus(403);

        $this->assertDatabaseHas('admins', ['id' => $target->id, 'status' => 1]);
    }

    public function test_admin_can_delete_a_non_last_other_account(): void
    {
        // Sanity: normal deletion still works when it is safe.
        $actor  = $this->makeAccount('admin');
        $victim = $this->makeAccount('cashier');

        $this->actingAs($actor, 'admin')
            ->delete(route('admin.accounts.destroy', $victim->id))
            ->assertOk();

        $this->assertDatabaseMissing('admins', ['id' => $victim->id]);
    }
}
