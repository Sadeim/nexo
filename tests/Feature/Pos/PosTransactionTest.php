<?php

namespace Tests\Feature\Pos;

use App\Models\Admin;
use App\Models\PosTransaction;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * HTTP-level checks for the money-persisting POS path.
 *
 * Uses DatabaseTransactions: every test runs inside a DB transaction that is
 * rolled back afterwards, so it exercises the REAL schema/data without leaving
 * anything behind in the dev database. Fixtures are created per-test.
 *
 * Relies on the seeded `pos.access` permission + `cashier` role already
 * present in the database (php artisan db:seed --class=PosSeeder).
 */
class PosTransactionTest extends TestCase
{
    use DatabaseTransactions;

    private function makeCashier(): Admin
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $cashier = Admin::create([
            'name'     => 'Test Cashier',
            'username' => 'test_cashier_' . uniqid(),
            'email'    => 'tc_' . uniqid() . '@nexo.local',
            'password' => Hash::make('secret123'),
            'status'   => 1,
        ]);

        $role = Role::where('name', 'cashier')->where('guard_name', 'admin')->firstOrFail();
        $cashier->assignRole($role);

        return $cashier;
    }

    private function pricedService(string $price = '200.00'): Service
    {
        return Service::create(['name' => 'Test Service ' . uniqid(), 'price' => $price]);
    }

    private function unpricedService(): Service
    {
        return Service::create(['name' => 'Unpriced ' . uniqid(), 'price' => null]);
    }

    public function test_guest_cannot_open_pos(): void
    {
        $this->get(route('pos.index'))->assertRedirect(route('pos.login'));
    }

    public function test_guest_cannot_post_transaction(): void
    {
        $service = $this->pricedService();
        $this->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            'items' => [['service_id' => $service->id, 'quantity' => 1]],
        ])->assertStatus(401);
    }

    public function test_cashier_can_complete_a_cash_sale(): void
    {
        $cashier = $this->makeCashier();
        $service = $this->pricedService('200.00');

        $res = $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            'items' => [['service_id' => $service->id, 'quantity' => 2]],
        ]);

        $res->assertStatus(201)
            ->assertJson(['success' => true, 'total' => '400.00']);

        $this->assertDatabaseHas('pos_transactions', [
            'id'       => $res->json('transaction_id'),
            'admin_id' => $cashier->id,
            'total'    => '400.00',
            'payment_method' => 'cash',
            'status'   => 'completed',
        ]);
        $this->assertDatabaseHas('pos_transaction_items', [
            'pos_transaction_id' => $res->json('transaction_id'),
            'service_id'     => $service->id,
            'original_price' => '200.00',
            'unit_price'     => '200.00',
            'quantity'       => 2,
            'line_total'     => '400.00',
        ]);
    }

    public function test_custom_price_is_used_but_original_is_snapshotted(): void
    {
        $cashier = $this->makeCashier();
        $service = $this->pricedService('200.00');

        $res = $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            'items' => [['service_id' => $service->id, 'quantity' => 1, 'custom_price' => '175.50']],
        ]);

        $res->assertStatus(201)->assertJson(['total' => '175.50']);

        $this->assertDatabaseHas('pos_transaction_items', [
            'service_id'     => $service->id,
            'original_price' => '200.00',  // stored price untouched
            'unit_price'     => '175.50',  // custom price charged
            'line_total'     => '175.50',
        ]);
        // The stored service price is unchanged.
        $this->assertSame('200.00', (string) $service->fresh()->price);
    }

    public function test_server_ignores_any_client_supplied_total(): void
    {
        $cashier = $this->makeCashier();
        $service = $this->pricedService('200.00');

        $res = $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            // Malicious/incorrect totals in the payload must be ignored.
            'subtotal' => '1.00',
            'total'    => '1.00',
            'items' => [['service_id' => $service->id, 'quantity' => 1]],
        ]);

        $res->assertStatus(201)->assertJson(['total' => '200.00']);
    }

    public function test_null_priced_service_cannot_be_sold(): void
    {
        $cashier = $this->makeCashier();
        $service = $this->unpricedService();

        $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            'items' => [['service_id' => $service->id, 'quantity' => 1]],
        ])->assertStatus(422);

        $this->assertDatabaseMissing('pos_transaction_items', ['service_id' => $service->id]);
    }

    public function test_empty_cart_is_rejected(): void
    {
        $cashier = $this->makeCashier();

        $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            'items' => [],
        ])->assertStatus(422);
    }

    public function test_zero_custom_price_is_allowed_as_a_comp_line(): void
    {
        // Policy change (edit #5): a deliberate custom price of 0 is a valid
        // comp/free line and completes the sale at $0.00. The original stored
        // price is still snapshotted.
        $cashier = $this->makeCashier();
        $service = $this->pricedService('200.00');

        $res = $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            'items' => [['service_id' => $service->id, 'quantity' => 1, 'custom_price' => 0]],
        ]);

        $res->assertStatus(201)->assertJson(['total' => '0.00']);
        $this->assertDatabaseHas('pos_transaction_items', [
            'service_id'     => $service->id,
            'original_price' => '200.00',
            'unit_price'     => '0.00',
            'line_total'     => '0.00',
        ]);
    }

    public function test_negative_custom_price_is_rejected(): void
    {
        $cashier = $this->makeCashier();
        $service = $this->pricedService('200.00');

        $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'cash',
            'items' => [['service_id' => $service->id, 'quantity' => 1, 'custom_price' => -5]],
        ])->assertStatus(422);

        $this->assertDatabaseMissing('pos_transaction_items', ['service_id' => $service->id]);
    }

    public function test_card_payment_is_not_available_yet(): void
    {
        $cashier = $this->makeCashier();
        $service = $this->pricedService('200.00');

        $this->actingAs($cashier, 'pos')->postJson(route('pos.transactions.store'), [
            'payment_method' => 'card',
            'items' => [['service_id' => $service->id, 'quantity' => 1]],
        ])->assertStatus(422)
          ->assertJson(['success' => false]);

        $this->assertDatabaseMissing('pos_transaction_items', ['service_id' => $service->id]);
    }
}
