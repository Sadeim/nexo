<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\PosPaymentWebhookEvent;
use App\Models\PosTransaction;
use App\Services\Pos\Payment\PlutoPaySignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Inbound PlutoPay webhooks — the ONLY authority that settles a card sale.
 *
 * Security order (must not change):
 *   1. Read the RAW body.
 *   2. Verify the HMAC-SHA256 signature on the raw body BEFORE json_decode.
 *      (300s replay window is inside PlutoPaySignature.)
 *   3. De-dupe on X-PlutoPay-Delivery (unique row) — same event may arrive twice.
 *   4. Apply the business effect idempotently.
 *   5. Return 2xx quickly (no outbound calls here).
 */
class PlutoPayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1) RAW body first — never trust a parsed body for signing.
        $rawBody = $request->getContent();
        $sigHeader = (string) $request->header('X-PlutoPay-Signature', '');
        $secret = (string) config('services.plutopay.webhook_secret');

        // 2) Verify signature (+ replay window) before decoding anything.
        if ($secret === '' || !PlutoPaySignature::verify($rawBody, $sigHeader, $secret)) {
            Log::warning('PlutoPay webhook rejected: bad signature');
            return response()->json(['error' => 'invalid signature'], 400);
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            return response()->json(['error' => 'invalid payload'], 400);
        }

        $deliveryId = (string) ($request->header('X-PlutoPay-Delivery') ?: ($payload['id'] ?? ''));
        if ($deliveryId === '') {
            return response()->json(['error' => 'missing delivery id'], 400);
        }

        $eventType       = (string) ($payload['type'] ?? $request->header('X-PlutoPay-Event', ''));
        $paymentIntentId = (string) ($payload['data']['id'] ?? '');

        // 3) De-dupe. Unique delivery_id guarantees at-most-once processing.
        try {
            $event = PosPaymentWebhookEvent::create([
                'delivery_id'       => $deliveryId,
                'event_type'        => $eventType,
                'payment_intent_id' => $paymentIntentId ?: null,
                'payload'           => $payload,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Duplicate delivery — already recorded/handled. Ack and move on.
            return response()->json(['received' => true, 'duplicate' => true], 200);
        }

        // 4) Apply the effect.
        $this->applyEffect($eventType, $paymentIntentId, $payload);
        $event->update(['processed_at' => now()]);

        // 5) Fast 2xx.
        return response()->json(['received' => true], 200);
    }

    private function applyEffect(string $eventType, string $paymentIntentId, array $payload): void
    {
        if ($paymentIntentId === '') {
            return; // Non-payment event (refund/payout/etc.) — nothing to settle here.
        }

        $transaction = PosTransaction::where('payment_intent_id', $paymentIntentId)->first();
        if (!$transaction) {
            Log::info('PlutoPay webhook for unknown intent', ['intent' => $paymentIntentId, 'type' => $eventType]);
            return;
        }

        DB::transaction(function () use ($eventType, $payload, $transaction) {
            // Re-fetch & lock to avoid clobbering a concurrent update.
            $txn = PosTransaction::whereKey($transaction->id)->lockForUpdate()->first();

            // Terminal states are final — never move a completed sale.
            if (in_array($txn->status, ['completed', 'failed', 'canceled'], true)) {
                return;
            }

            if ($eventType === 'payment.succeeded') {
                $txn->update([
                    'status'    => 'completed',
                    'reference' => $payload['data']['reference'] ?? $txn->reference,
                ]);
            } elseif ($eventType === 'payment.failed') {
                $txn->update([
                    'status'         => 'failed',
                    'failure_reason' => $payload['data']['failure_reason']
                        ?? ($payload['data']['status'] ?? 'card_declined'),
                ]);
            }
            // Other event types: recorded for audit, no state change.
        });
    }
}
