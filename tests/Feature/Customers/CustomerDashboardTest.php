<?php

namespace Tests\Feature\Customers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * The owner's view of the customer list, and the export that is the point of
 * collecting any of it.
 */
class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Admin $owner;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $view = Permission::firstOrCreate(
            ['name' => 'view_bookings', 'guard_name' => 'admin'],
            ['name_key' => 'view', 'parent' => 'bookings']
        );

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $role->givePermissionTo($view);

        $this->owner = Admin::create([
            'name' => 'Owner',
            'email' => 'owner_'.uniqid().'@nexo.local',
            'password' => Hash::make('Password123'),
            'status' => 1,
        ]);

        $this->owner->assignRole($role);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // ---------- the screens ----------

    public function test_the_customer_list_renders(): void
    {
        Customer::factory()->consented()->create(['first_name' => 'Omar', 'last_name' => 'Haddad']);
        Customer::factory()->create(['first_name' => 'Eddie', 'last_name' => 'Nasser']);

        $this->actingAs($this->owner, 'admin')
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Customers')
            // The count and the word are separate nodes in the markup.
            ->assertSee('<strong>2</strong> customers in total', false)
            ->assertSee('Export 1 consented number (CSV)');
    }

    public function test_the_list_renders_with_no_customers_at_all(): void
    {
        $this->actingAs($this->owner, 'admin')
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Export 0 consented numbers (CSV)');
    }

    public function test_the_datatable_returns_the_customers(): void
    {
        $customer = Customer::factory()->consented()->create([
            'phone' => '2246698063', 'first_name' => 'Omar', 'last_name' => 'Haddad',
        ]);

        $body = $this->datatable();

        $this->assertStringContainsString('Omar Haddad', $body);
        // Shown as people read numbers, linked as phones dial them.
        $this->assertStringContainsString('(224) 669-8063', $body);
        $this->assertStringContainsString('tel:+12246698063', $body);
        $this->assertStringContainsString((string) $customer->id, $body);
    }

    public function test_the_list_can_be_searched_by_phone_or_name(): void
    {
        Customer::factory()->create(['phone' => '2246698063', 'first_name' => 'Omar', 'last_name' => 'Haddad']);
        Customer::factory()->create(['phone' => '3125550000', 'first_name' => 'Eddie', 'last_name' => 'Nasser']);

        foreach (['Omar', 'Haddad', '2246698063', '(224) 669'] as $term) {
            $body = $this->datatable(['search' => ['value' => $term]]);

            $this->assertStringContainsString('Omar', $body, "Searching for $term did not find Omar.");
            $this->assertStringNotContainsString('Eddie', $body, "Searching for $term also returned Eddie.");
        }
    }

    public function test_a_customer_detail_page_lists_their_bookings(): void
    {
        $customer = Customer::factory()->create(['first_name' => 'Omar', 'last_name' => 'Haddad']);

        Booking::factory()->create([
            'customer_id' => $customer->id,
            'date' => '2026-11-11',
            'time' => '10:00:00',
            'name' => 'Omar Haddad',
        ]);

        $this->actingAs($this->owner, 'admin')
            ->get(route('admin.customers.show', $customer->id))
            ->assertOk()
            ->assertSee('Omar Haddad')
            ->assertSee('Wed 11 Nov 2026')
            ->assertSee('10:00 AM');
    }

    public function test_a_customer_with_only_a_phone_number_renders(): void
    {
        $customer = Customer::factory()->anonymous()->create(['phone' => '2246698063']);

        $this->actingAs($this->owner, 'admin')
            ->get(route('admin.customers.show', $customer->id))
            ->assertOk()
            // No name yet, so the number stands in for one.
            ->assertSee('(224) 669-8063')
            ->assertSee('No bookings linked to this customer yet');
    }

    public function test_the_customer_area_is_closed_to_anyone_without_the_permission(): void
    {
        $cashier = Admin::create([
            'name' => 'Cashier',
            'email' => 'cashier_'.uniqid().'@nexo.local',
            'password' => Hash::make('Password123'),
            'status' => 1,
        ]);

        $cashier->assignRole(Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'admin']));

        foreach (['index', 'datatable', 'export'] as $screen) {
            $this->actingAs($cashier, 'admin')
                ->get(route('admin.customers.'.$screen))
                ->assertForbidden();
        }
    }

    public function test_a_guest_cannot_reach_the_customer_list(): void
    {
        $this->get(route('admin.customers.index'))->assertRedirect();
        $this->get(route('admin.customers.export'))->assertRedirect();
    }

    // ---------- the export ----------

    /**
     * The one assertion this whole feature answers to. Everything in this
     * file is a number somebody ticked a box for.
     */
    public function test_the_export_contains_only_consented_numbers(): void
    {
        Customer::factory()->consented()->create(['phone' => '2246698063', 'first_name' => 'Omar']);
        Customer::factory()->consented()->create(['phone' => '3125550000', 'first_name' => 'Eddie']);
        Customer::factory()->create(['phone' => '7735551111', 'first_name' => 'Refused']);
        Customer::factory()->anonymous()->create(['phone' => '8475552222']);

        $csv = $this->download();

        $this->assertStringContainsString('+12246698063', $csv);
        $this->assertStringContainsString('+13125550000', $csv);

        $this->assertStringNotContainsString('7735551111', $csv);
        $this->assertStringNotContainsString('Refused', $csv);
        $this->assertStringNotContainsString('8475552222', $csv);

        // Header plus exactly two rows.
        $this->assertCount(3, array_filter(explode("\n", trim($csv))));
    }

    public function test_the_export_carries_the_evidence_with_each_number(): void
    {
        $this->travelTo('2026-11-10 19:30:00');

        Customer::factory()->consented()->create([
            'phone' => '2246698063', 'first_name' => 'Omar', 'last_name' => 'Haddad',
        ]);

        $csv = $this->download();

        $this->assertStringContainsString('phone,first_name,last_name,email,consent_given_at,consent_source', $csv);
        $this->assertStringContainsString('2026-11-10T19:30:00', $csv);
        $this->assertStringContainsString('booking_modal', $csv);
    }

    public function test_an_export_with_nobody_consenting_is_just_a_header(): void
    {
        Customer::factory()->count(3)->create();

        $csv = $this->download();

        $this->assertCount(1, array_filter(explode("\n", trim($csv))));
    }

    /**
     * Nothing in the request may widen the list. If a parameter could ever
     * turn this into "everybody", the consent record would be decoration.
     */
    public function test_no_parameter_can_widen_the_export(): void
    {
        Customer::factory()->consented()->create(['phone' => '2246698063']);
        Customer::factory()->create(['phone' => '7735551111', 'first_name' => 'Refused']);

        foreach ([
            ['all' => 1],
            ['consented' => 0],
            ['sms_marketing_consent' => 0],
            ['filter' => 'all'],
            ['scope' => 'everyone'],
        ] as $query) {
            $csv = $this->download($query);

            $this->assertStringNotContainsString('7735551111', $csv,
                'The export widened with '.json_encode($query));
        }
    }

    /**
     * A datatable request as the browser actually makes it.
     *
     * start and length matter: the shared filterDataTable() helper defaults
     * start to 1 rather than 0 when the parameter is missing, so a bare
     * request skips the first row. DataTables always sends them; a test that
     * does not would be asserting against a page nobody ever sees.
     */
    private function datatable(array $query = []): string
    {
        return $this->actingAs($this->owner, 'admin')
            ->get(route('admin.customers.datatable', $query + ['start' => 0, 'length' => 10]))
            ->assertOk()
            ->getContent();
    }

    private function download(array $query = []): string
    {
        $response = $this->actingAs($this->owner, 'admin')
            ->get(route('admin.customers.export', $query));

        $response->assertOk();

        return $response->streamedContent();
    }
}
