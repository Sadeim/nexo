<?php

namespace Tests\Feature\Portal;

use App\Http\Middleware\UseEmployeeSession;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\SeedsAccessControl;
use Tests\TestCase;

/**
 * The owner's half: giving a barber a phone and a password from the dashboard,
 * and the session separation that lets the owner do it while signed in as
 * themselves.
 */
class PortalSetupTest extends TestCase
{
    use RefreshDatabase;
    use SeedsAccessControl;

    private Admin $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedAccessControl();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->owner = Admin::create([
            'name' => 'Owner',
            'email' => 'owner_'.uniqid().'@nexo.local',
            'password' => Hash::make('Password123'),
            'status' => 1,
        ]);

        $this->owner->assignRole(
            Role::where('name', 'admin')->where('guard_name', 'admin')->firstOrFail()
        );
    }

    // ---------- phone ----------

    public function test_the_owner_can_give_a_barber_a_phone_number(): void
    {
        $barber = Employee::factory()->create(['phone' => null]);

        $this->actingAs($this->owner, 'admin')
            ->patchJson(route('admin.employees.update', $barber->id), [
                'name' => $barber->name,
                'phone' => '(224) 669-8063',
            ])->assertSuccessful();

        // Stored as digits, so login can match whatever form is typed later.
        $this->assertSame('2246698063', $barber->fresh()->phone);
    }

    public function test_two_barbers_cannot_share_a_phone_number(): void
    {
        Employee::factory()->create(['phone' => '2246698063']);
        $second = Employee::factory()->create(['phone' => null]);

        $this->actingAs($this->owner, 'admin')
            ->patchJson(route('admin.employees.update', $second->id), [
                'name' => $second->name,
                'phone' => '224-669-8063',
            ])->assertStatus(422);

        $this->assertNull($second->fresh()->phone);
    }

    public function test_a_barber_can_be_saved_with_no_phone_at_all(): void
    {
        $barber = Employee::factory()->create(['phone' => null]);

        $this->actingAs($this->owner, 'admin')
            ->patchJson(route('admin.employees.update', $barber->id), [
                'name' => $barber->name,
                'phone' => '',
            ])->assertSuccessful();

        $this->assertNull($barber->fresh()->phone);
    }

    /**
     * A save that does not carry the phone field must leave the stored number
     * alone. Normalising unconditionally would null it out on every such save.
     */
    public function test_a_save_without_the_phone_field_keeps_the_existing_number(): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $this->actingAs($this->owner, 'admin')
            ->patchJson(route('admin.employees.update', $barber->id), [
                'name' => $barber->name,
                'commission_rate' => 40,
            ])->assertSuccessful();

        $this->assertSame('2246698063', $barber->fresh()->phone,
            'An unrelated save wiped the barber\'s portal phone number.');
    }

    // ---------- password ----------

    public function test_the_owner_sets_a_temporary_password(): void
    {
        $barber = Employee::factory()->create(['phone' => '2246698063', 'password' => null]);

        $this->actingAs($this->owner, 'admin')
            ->postJson(route('admin.employees.portal_password', $barber->id), [
                'password' => 'temporary-123',
            ])->assertSuccessful();

        $barber->refresh();

        $this->assertTrue(Hash::check('temporary-123', $barber->password));
        $this->assertTrue($barber->must_change_password,
            'A password typed by the owner must be a one-time one.');
        $this->assertTrue($barber->hasPortalAccess());
    }

    public function test_resetting_a_password_forces_the_change_again(): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $this->assertFalse($barber->must_change_password);

        $this->actingAs($this->owner, 'admin')
            ->postJson(route('admin.employees.portal_password', $barber->id), [
                'password' => 'reset-again-123',
            ])->assertSuccessful();

        $this->assertTrue($barber->fresh()->must_change_password);
    }

    public function test_a_password_cannot_be_set_without_a_phone(): void
    {
        $barber = Employee::factory()->create(['phone' => null]);

        $this->actingAs($this->owner, 'admin')
            ->postJson(route('admin.employees.portal_password', $barber->id), [
                'password' => 'temporary-123',
            ])->assertStatus(422);

        $this->assertNull($barber->fresh()->password);
    }

    /** Validation reaches the dashboard as 422 JSON, which is what its Swal reads. */
    public function test_a_short_password_is_refused(): void
    {
        $barber = Employee::factory()->create(['phone' => '2246698063']);

        $this->actingAs($this->owner, 'admin')
            ->postJson(route('admin.employees.portal_password', $barber->id), [
                'password' => 'short',
            ])->assertStatus(422);
    }

    public function test_only_an_admin_can_set_a_portal_password(): void
    {
        $barber = Employee::factory()->create(['phone' => '2246698063']);

        // A plain browser request, so an unauthenticated POST redirects to the
        // admin login rather than answering 401.
        $this->post(route('admin.employees.portal_password', $barber->id), [
            'password' => 'temporary-123',
        ])->assertRedirect();

        $this->assertNull($barber->fresh()->password);
    }

    /**
     * An employee session must not be an admin one. If it were, a barber
     * could set their own commission rate.
     */
    public function test_an_employee_session_cannot_set_a_portal_password(): void
    {
        $barber = Employee::factory()->withPortal()->create();
        $victim = Employee::factory()->create(['phone' => '2246698063']);

        $response = $this->actingAs($barber, 'employee')
            ->postJson(route('admin.employees.portal_password', $victim->id), [
                'password' => 'temporary-123',
            ]);

        $this->assertNotEquals(200, $response->getStatusCode());
        $this->assertNull($victim->fresh()->password);
    }

    // ---------- the dashboard form ----------

    public function test_the_form_reports_whether_the_portal_is_set_up(): void
    {
        $notSetUp = Employee::factory()->create(['phone' => null, 'password' => null]);

        $this->actingAs($this->owner, 'admin')
            ->get(route('admin.employees.edit', $notSetUp->id))
            ->assertOk()
            ->assertSee('Not set up')
            ->assertSee('name="phone"', false);

        $active = Employee::factory()->withPortal('2246698063')->create();

        $this->actingAs($this->owner, 'admin')
            ->get(route('admin.employees.edit', $active->id))
            ->assertOk()
            ->assertSee('Active')
            ->assertSee('Reset password');
    }

    public function test_the_form_never_renders_the_password_hash(): void
    {
        $barber = Employee::factory()->withPortal('2246698063')->create();

        $response = $this->actingAs($this->owner, 'admin')
            ->get(route('admin.employees.edit', $barber->id));

        $this->assertStringNotContainsString($barber->fresh()->password, $response->getContent());
    }

    // ---------- session separation ----------

    /**
     * The portal's session rides a different cookie, so the owner can be
     * signed in to the dashboard and to a barber's portal in one browser
     * without either displacing the other — and so logging out of the portal
     * does not end the admin session.
     */
    public function test_the_portal_uses_its_own_session_cookie(): void
    {
        $default = config('session.cookie');

        $this->assertNotSame($default, UseEmployeeSession::COOKIE,
            'The portal cookie is the same as the app cookie, so sessions will collide.');

        $portal = $this->get(route('portal.login'));
        $portal->assertOk();

        $this->assertContains(
            UseEmployeeSession::COOKIE,
            collect($portal->headers->getCookies())->map->getName()->all(),
            'The portal login did not set the portal session cookie.'
        );
    }

    public function test_a_page_outside_the_portal_keeps_the_app_cookie(): void
    {
        $response = $this->get(route('admin.login'));

        $names = collect($response->headers->getCookies())->map->getName()->all();

        $this->assertNotContains(UseEmployeeSession::COOKIE, $names,
            'The portal session cookie leaked onto a non-portal page.');
    }
}
