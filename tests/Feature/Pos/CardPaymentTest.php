<?php

namespace Tests\Feature\Pos;

use App\Models\Admin;
use App\Models\PosTransaction;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * End-to-end (TEST MODE) coverage for the PlutoPay card flow.
 *
 * Uses DatabaseTransactions against the real schema and Http::fake() so no
 * request ever leaves the machine. Core invariant under test: a sale is only
 * ever PAID after a verified payment.succeeded webhook.
 */
class CardPaymentTest extends TestCase
{
    use DatabaseTransactions;

    private string $webhookSecret = 'whsec_test_secret';

    protected function setUp(): void
    {
        parent::setUp();

        // Lock the gateway to TEST MODE for the whole suite.
        config()->set('services.plutopay.secret_key', 'sk_test_dummy');
        config()->set('services.plutopay.webhook_secret', $this->webhookSecret);
        config()->set('services.plutopay.terminal_id', 'tmr_test');
        config()->set('services.plutopay.reader_id', 'rdr_test');
        config()->set('services.plutopay.currency', 'usd');
    }

    private function makeCashier(): Admin
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $cashier = Admin::create([
            'name'     => 'Test Cashier',
            'username' => 'card_cashier_' . uniqid(),
            'email'    => 'cc_' . uniqid() . '@nexo.local',
            'password' => Hash::make('secret123'),
            'status'   => 1,
        ]);
        $cashier->assignRole(Role::where('name', 'cashier')->where('guard_name', 'admin')->firstOrFail());

        return $cashier;
    }

    private function service(string $price): Service
    {
        return Service::create(['name' => 'Card Svc ' . uniqid(), 'price' => $price]);
    }

    /** Fake the 3-step terminal flow. create-payment returns intent pi_123. */
    private function fakeTerminalOk(): void
    {
        Http::fake([
            '*connection-token' => Http::response(['data' => ['secret' => 'ct_x']], 200),
            '*create-payment'   => Http::response(['data' => [
                'id' => 'uuid_123', 'payment_intent_id' => 'pi_123',
                'reference' => 'ref_1', 'client_secret' => 'cs_1',
                'amount' => 4750, 'currency' => 'usd', 'status' => 'pending',
            ]], 200),
            '*process-payment'  => Http::response(['data' => [
                'status' => 'processing', 'reader_id' => 'rdr_test', 'action_type' => 'collect_payment',
            ]], 200),
        ]);
    }

    private function signedWebhook(array $payload): array
    {
        $raw = json_encode($payload);
        $ts = time();
        $sig = hash_hmac('sha256', "{$ts}.{$raw}", $this->webhookSecret);

        return [$raw, ['t' => $ts, 'v1' => $sig]];
    }

    private function postWebhook(array $payload, ?string $deliveryId = null, ?string $signatureHeader = null)
    {
        [$raw, $parts] = $this->signedWebhook($payload);
        $header = $signatureHeader ?? "t={$parts['t']},v1={$parts['v1']}";

        return $this->call(
            'POST',
            route('webhooks.plutopay'),
            [],
            [],
            [],
            [
                'HTTP_X-PlutoPay-Signature' => $header,
                'HTTP_X-PlutoPay-Delivery'  => $deliveryId ?? ('dlv_' . uniqid()),
                'HTTP_X-PlutoPay-Event'     => $payload['type'] ?? '',
                'CONTENT_TYPE'              => 'application/json',
            ],
            $raw
        );
    }

    // ---- start() ----

    public function test_card_start_creates_pending_and_is_not_paid_yet(): void
    {
        $this->fakeTerminalOk();
        $cashier = $this->makeCashier();
        $svc = $this->service('47.50');

        $res = $this->actingAs($cashier, 'pos')->postJson(route('pos.card.start'), [
            'idempotency_key' => 'attempt-' . uniqid(),
            'items' => [['service_id' => $svc->id, 'quantity' => 1]],
        ]);

        $res->assertOk()->assertJson(['success' => true, 'paid' => false, 'status' => 'processing']);

        $this->assertDatabaseHas('pos_transactions', [
            'id'                => $res->json('transaction_id'),
            'payment_method'    => 'card',
            'status'            => 'processing',
            'payment_intent_id' => 'pi_123',
            'amount_cents'      => 4750,
        ]);
    }

    public function test_card_rejects_amount_below_minimum(): void
    {
        $cashier = $this->makeCashier();
        $svc = $this->service('0.25'); // 25 cents < 50

        $this->actingAs($cashier, 'pos')->postJson(route('pos.card.start'), [
            'idempotency_key' => 'attempt-' . uniqid(),
            'items' => [['service_id' => $svc->id, 'quantity' => 1]],
        ])->assertStatus(422)->assertJson(['success' => false]);
    }

    public function test_double_submit_same_key_creates_one_transaction(): void
    {
        $this->fakeTerminalOk();
        $cashier = $this->makeCashier();
        $svc = $this->service('47.50');
        $key = 'attempt-' . uniqid();
        $body = ['idempotency_key' => $key, 'items' => [['service_id' => $svc->id, 'quantity' => 1]]];

        $r1 = $this->actingAs($cashier, 'pos')->postJson(route('pos.card.start'), $body);
        $r2 = $this->actingAs($cashier, 'pos')->postJson(route('pos.card.start'), $body);

        $r1->assertOk();
        $r2->assertOk();
        $this->assertSame($r1->json('transaction_id'), $r2->json('transaction_id'));
        $this->assertSame(1, PosTransaction::where('idempotency_key', $key)->count());
    }

    // ---- webhook settlement ----

    public function test_webhook_succeeded_marks_completed(): void
    {
        $cashier = $this->makeCashier();
        $txn = PosTransaction::create([
            'admin_id' => $cashier->id, 'subtotal' => '47.50', 'total' => '47.50',
            'amount_cents' => 4750, 'currency' => 'usd', 'payment_method' => 'card',
            'status' => 'processing', 'payment_intent_id' => 'pi_abc',
            'idempotency_key' => 'k_' . uniqid(),
        ]);

        $res = $this->postWebhook([
            'id' => 'evt_1', 'type' => 'payment.succeeded',
            'data' => ['id' => 'pi_abc', 'status' => 'succeeded', 'reference' => 'REF-9'],
        ]);

        $res->assertOk();
        $txn->refresh();
        $this->assertSame('completed', $txn->status);
        $this->assertSame('REF-9', $txn->reference);
    }

    public function test_webhook_failed_marks_failed_and_not_paid(): void
    {
        $cashier = $this->makeCashier();
        $txn = PosTransaction::create([
            'admin_id' => $cashier->id, 'subtotal' => '47.50', 'total' => '47.50',
            'amount_cents' => 4750, 'currency' => 'usd', 'payment_method' => 'card',
            'status' => 'processing', 'payment_intent_id' => 'pi_fail',
            'idempotency_key' => 'k_' . uniqid(),
        ]);

        $this->postWebhook([
            'id' => 'evt_2', 'type' => 'payment.failed',
            'data' => ['id' => 'pi_fail', 'status' => 'declined', 'failure_reason' => 'card_declined'],
        ])->assertOk();

        $txn->refresh();
        $this->assertSame('failed', $txn->status);
        $this->assertSame('card_declined', $txn->failure_reason);
    }

    public function test_webhook_invalid_signature_is_rejected_and_no_state_change(): void
    {
        $cashier = $this->makeCashier();
        $txn = PosTransaction::create([
            'admin_id' => $cashier->id, 'subtotal' => '47.50', 'total' => '47.50',
            'amount_cents' => 4750, 'currency' => 'usd', 'payment_method' => 'card',
            'status' => 'processing', 'payment_intent_id' => 'pi_bad',
            'idempotency_key' => 'k_' . uniqid(),
        ]);

        $this->postWebhook(
            ['id' => 'evt_3', 'type' => 'payment.succeeded', 'data' => ['id' => 'pi_bad', 'status' => 'succeeded']],
            null,
            't=' . time() . ',v1=deadbeef' // bad signature
        )->assertStatus(400);

        $txn->refresh();
        $this->assertSame('processing', $txn->status); // unchanged — not paid
    }

    public function test_webhook_is_idempotent_on_delivery_id(): void
    {
        $cashier = $this->makeCashier();
        $txn = PosTransaction::create([
            'admin_id' => $cashier->id, 'subtotal' => '47.50', 'total' => '47.50',
            'amount_cents' => 4750, 'currency' => 'usd', 'payment_method' => 'card',
            'status' => 'processing', 'payment_intent_id' => 'pi_dup',
            'idempotency_key' => 'k_' . uniqid(),
        ]);
        $delivery = 'dlv_fixed_' . uniqid();
        $payload = ['id' => 'evt_4', 'type' => 'payment.succeeded', 'data' => ['id' => 'pi_dup', 'status' => 'succeeded', 'reference' => 'R1']];

        $this->postWebhook($payload, $delivery)->assertOk();
        $second = $this->postWebhook($payload, $delivery)->assertOk();

        $second->assertJson(['duplicate' => true]);
        $this->assertSame(1, \DB::table('pos_payment_webhook_events')->where('delivery_id', $delivery)->count());
        $this->assertSame('completed', $txn->refresh()->status);
    }
}
