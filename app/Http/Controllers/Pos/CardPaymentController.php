<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StartCardPaymentRequest;
use App\Models\PosTransaction;
use App\Services\Pos\CartResolver;
use App\Services\Pos\Exceptions\InvalidPriceException;
use App\Services\Pos\Payment\Exceptions\PlutoPayException;
use App\Services\Pos\Payment\PlutoPayClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Card (PlutoPay Terminal) checkout.
 *
 * Flow:
 *   start()  -> compute the amount SERVER-SIDE, create/reuse a pending
 *               (awaiting_payment) transaction, then run the documented
 *               3-step terminal flow (connection-token, create-payment,
 *               process-payment). The response does NOT settle the sale.
 *   status() -> polled by the cashier UI until a webhook flips the row to
 *               completed / failed.
 *
 * 🔴 A transaction is only ever considered PAID by the webhook handler.
 */
class CardPaymentController extends Controller
{
    public function __construct(
        private CartResolver $cart,
    ) {
    }

    /**
     * Resolve the PlutoPay client lazily. Its constructor enforces the TEST-MODE
     * guard, so we only build it when a card operation truly needs it — never at
     * route registration / app boot, and never on the status() poll path.
     */
    private function pluto(): PlutoPayClient
    {
        return app(PlutoPayClient::class);
    }

    public function start(StartCardPaymentRequest $request)
    {
        $key = $request->input('idempotency_key');

        // Idempotent replay: if this attempt already produced a row, just
        // report where it stands — never create a second charge.
        $existing = PosTransaction::where('idempotency_key', $key)->first();
        if ($existing) {
            return $this->driveAndRespond($existing);
        }

        // 1) Authoritative amount from the DB-priced cart.
        try {
            $cart = $this->cart->resolve($request->input('items', []));
        } catch (InvalidPriceException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $amountCents = $cart['total_cents'];
        $min = (int) config('services.plutopay.min_amount_cents', 50);
        if ($amountCents < $min) {
            return response()->json([
                'success' => false,
                'message' => 'Card payments require a minimum of $' . number_format($min / 100, 2) . '.',
            ], 422);
        }

        // 2) Persist a pending transaction (+ item snapshots) atomically. The
        // UNIQUE idempotency_key means a racing double-click hits the catch and
        // reuses the winner's row.
        try {
            $transaction = DB::transaction(function () use ($cart, $key, $amountCents) {
                $transaction = PosTransaction::create([
                    'admin_id'        => Auth::guard('pos')->id(),
                    'subtotal'        => $cart['subtotal'],
                    'total'           => $cart['total'],
                    'amount_cents'    => $amountCents,
                    'currency'        => config('services.plutopay.currency', 'usd'),
                    'payment_method'  => 'card',
                    'status'          => 'awaiting_payment',
                    'idempotency_key' => $key,
                ]);

                foreach ($cart['items'] as $item) {
                    $transaction->items()->create([
                        'service_id'     => $item['service_id'],
                        'service_name'   => $item['service_name'],
                        'original_price' => $item['original_price'],
                        'unit_price'     => $item['unit_price'],
                        'quantity'       => $item['quantity'],
                        'line_total'     => $item['line_total'],
                    ]);
                }

                return $transaction;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Lost the race on the unique key: reuse the existing row.
            $transaction = PosTransaction::where('idempotency_key', $key)->firstOrFail();
        }

        return $this->driveAndRespond($transaction);
    }

    /**
     * Runs the terminal flow for a transaction that still needs a card read,
     * then returns the current status. Safe to call more than once thanks to
     * the PlutoPay Idempotency-Key on create-payment.
     */
    private function driveAndRespond(PosTransaction $transaction)
    {
        // Already settled (webhook won) — nothing to do.
        if (in_array($transaction->status, ['completed', 'failed', 'canceled'], true)) {
            return $this->statusPayload($transaction, 200);
        }

        // Reader is already processing this intent — don't re-trigger.
        if ($transaction->status === 'processing' && $transaction->payment_intent_id) {
            return $this->statusPayload($transaction, 200);
        }

        try {
            // Step 2 (docs): connection token — initialises the reader session.
            $this->pluto()->connectionToken();

            // Step 3: create the payment intent (idempotent on our key).
            if (!$transaction->payment_intent_id) {
                $payment = $this->pluto()->createPayment(
                    (int) $transaction->amount_cents,
                    'pos-card-' . $transaction->idempotency_key,
                    ['pos_transaction_id' => $transaction->id],
                );

                $transaction->update([
                    'payment_intent_id' => $payment['id'],
                    'reference'         => $payment['reference'],
                ]);
            }

            // Step 4: start the read on the physical reader.
            $this->pluto()->processPayment($transaction->payment_intent_id);

            $transaction->update(['status' => 'processing']);
        } catch (PlutoPayException $e) {
            Log::warning('PlutoPay start failed', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
            ]);

            // Leave the row awaiting_payment (reconcilable). Never settle here.
            return response()->json([
                'success'        => false,
                'transaction_id' => $transaction->id,
                'status'         => $transaction->status,
                'message'        => 'Could not start the card reader: ' . $e->getMessage(),
            ], 502);
        }

        return $this->statusPayload($transaction, 200);
    }

    /**
     * Polled by the UI. Scoped to the current cashier so one cashier can't read
     * another's transaction.
     */
    public function status(PosTransaction $transaction)
    {
        if ($transaction->admin_id !== Auth::guard('pos')->id()) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return $this->statusPayload($transaction, 200);
    }

    private function statusPayload(PosTransaction $transaction, int $code)
    {
        return response()->json([
            'success'        => true,
            'transaction_id' => $transaction->id,
            'status'         => $transaction->status,   // awaiting_payment|processing|completed|failed|canceled
            'paid'           => $transaction->status === 'completed',
            'failure_reason' => $transaction->failure_reason,
            'total'          => $transaction->total,
        ], $code);
    }
}
