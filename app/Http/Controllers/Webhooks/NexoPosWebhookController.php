<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Mail\PosReceiptMail;
use App\Models\PosApiWebhookEvent;
use App\Models\PosOrder;
use App\Services\NexoPos\Payment\PlutoPaySignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * PlutoPay webhooks aimed at the Flutter POS. Isolated from Haneen's
 * PlutoPayWebhookController (which settles pos_transactions for the Web POS).
 *
 * Order of operations (do not reorder):
 *   1. Read RAW body — never trust a parsed body for signing.
 *   2. Verify HMAC-SHA256 signature (+ 300s replay window).
 *   3. De-dupe on X-PlutoPay-Delivery via UNIQUE constraint.
 *   4. Apply the settlement effect idempotently.
 *   5. Fast 2xx.
 */
class NexoPosWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $rawBody   = $request->getContent();
        $sigHeader = (string) $request->header('X-PlutoPay-Signature', '');
        $secret    = (string) config('nexo_pos.plutopay.webhook_secret');

        if ($secret === '' || !PlutoPaySignature::verify($rawBody, $sigHeader, $secret)) {
            Log::warning('NexoPos PlutoPay webhook rejected: bad signature');
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

        try {
            $event = PosApiWebhookEvent::create([
                'delivery_id'       => $deliveryId,
                'event_type'        => $eventType,
                'payment_intent_id' => $paymentIntentId ?: null,
                'payload'           => $payload,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Duplicate delivery — already recorded/handled.
            return response()->json(['received' => true, 'duplicate' => true], 200);
        }

        $this->applyEffect($eventType, $paymentIntentId, $payload);
        $event->update(['processed_at' => now()]);

        return response()->json(['received' => true], 200);
    }

    private function applyEffect(string $eventType, string $paymentIntentId, array $payload): void
    {
        if ($paymentIntentId === '') {
            return; // Unrelated event (refund/payout/etc.)
        }

        // May reference pi_... OR the provider UUID depending on the event shape.
        $order = PosOrder::where('payment_intent_id', $paymentIntentId)
            ->orWhere('provider_payment_id', $paymentIntentId)
            ->first();

        if (!$order) {
            // Not one of ours — probably a Web POS event on the shared webhook.
            return;
        }

        DB::transaction(function () use ($eventType, $payload, $order) {
            /** @var PosOrder $fresh */
            $fresh = PosOrder::whereKey($order->id)->lockForUpdate()->first();

            // Terminal states are final.
            if ($fresh->isTerminal()) {
                return;
            }

            $isSuccess = in_array($eventType, ['payment.succeeded', 'payment_intent.succeeded'], true);
            $isFailed  = in_array($eventType, ['payment.failed', 'payment_intent.payment_failed'], true);

            if ($isSuccess) {
                // On-reader tipping: final amount + tip come from the webhook.
                $data       = $payload['data'] ?? [];
                $amountCents = (int) ($data['amount'] ?? $data['amount_captured'] ?? $fresh->amount_cents ?? 0);
                $tipCents    = (int) ($data['tip_amount'] ?? 0);

                $newTotal = $amountCents > 0 ? round($amountCents / 100, 2) : (float) $fresh->total;
                $newTip   = $tipCents > 0 ? round($tipCents / 100, 2) : (float) $fresh->tip;

                $fresh->update([
                    'status'    => 'completed',
                    'reference' => $data['reference'] ?? $fresh->reference,
                    'tip'       => $newTip,
                    'total'     => $newTotal,
                ]);

                $this->sendReceiptIfRequested($fresh->fresh(['items', 'employee']));
                return;
            }

            if ($isFailed) {
                $fresh->update([
                    'status'         => 'failed',
                    'failure_reason' => $payload['data']['failure_reason']
                        ?? ($payload['data']['status'] ?? 'card_declined'),
                ]);
                return;
            }

            // Other event types are recorded for audit; no state change.
        });
    }

    protected function sendReceiptIfRequested(PosOrder $order): void
    {
        if (empty($order->customer_email)) {
            return;
        }
        try {
            Mail::to($order->customer_email)->send(new PosReceiptMail($order));
            $order->forceFill(['receipt_sent_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::warning('NexoPos card receipt email failed', [
                'order_id' => $order->id,
                'err'      => $e->getMessage(),
            ]);
        }
    }
}
