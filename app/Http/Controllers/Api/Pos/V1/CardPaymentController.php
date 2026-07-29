<?php

namespace App\Http\Controllers\Api\Pos\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Services\NexoPos\Payment\Exceptions\PlutoPayException;
use App\Services\NexoPos\Payment\PlutoPayClient;
use App\Support\PosSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * PlutoPay Terminal card checkout for the Flutter POS.
 *
 * Flow:
 *   start()  → server-side amount, persist a pending pos_orders row (with an
 *              idempotency_key so a double-tap can't create two charges),
 *              call PlutoPay create-payment + process-payment. Row is
 *              awaiting_payment/processing on return.
 *   status() → polled by the tablet UI. Terminal states (completed/failed/
 *              canceled) come from the webhook, not this endpoint.
 *
 * A sale is ONLY ever settled by the webhook handler.
 */
class CardPaymentController extends Controller
{
    /** Lazily built so its TEST-MODE guard doesn't fire on unrelated routes. */
    private function pluto(): PlutoPayClient
    {
        return app(PlutoPayClient::class);
    }

    /**
     * List available terminal readers so the tablet can let the cashier pick
     * one (Loarien-style setup screen). Each entry carries an `id` (uuid, used
     * as `terminal_id` in create-payment) and `processor_terminal_id` (tmr_...,
     * the reader the customer physically taps).
     */
    public function readers()
    {
        try {
            $readers = $this->pluto()->listReaders();
        } catch (PlutoPayException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }

        return response()->json(['success' => true, 'readers' => $readers]);
    }

    public function start(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->where('is_active', true)],
            'idempotency_key' => 'required|string|min:8|max:64',
            'reader_id' => 'nullable|string|max:128',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.is_custom' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
            'customer_email' => 'nullable|email',
        ]);

        $admin = $request->attributes->get('pos_admin');

        // Compute subtotal server-side. Tip is charged on the reader itself, so
        // we always create the payment for the base amount only.
        $subtotal = 0.0;
        foreach ($data['items'] as $item) {
            $subtotal += ((float) $item['price']) * ($item['quantity'] ?? 1);
        }
        $subtotal = round($subtotal, 2);

        // Card surcharge on top of the services — the customer really pays it,
        // so it goes into the amount we send to the reader.
        $cardFee = PosSettings::cardFee();
        $chargeable = round($subtotal + $cardFee, 2);
        $amountCents = (int) round($chargeable * 100);

        $min = (int) config('nexo_pos.plutopay.min_amount_cents', 50);
        if ($amountCents < $min) {
            return response()->json([
                'success' => false,
                'message' => 'Card payments require a minimum of $' . number_format($min / 100, 2) . '.',
            ], 422);
        }

        // Idempotent replay
        $existing = PosOrder::where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing) {
            return $this->driveAndRespond($existing, $data['reader_id'] ?? null);
        }

        try {
            $order = DB::transaction(function () use ($data, $admin, $subtotal, $amountCents, $cardFee, $chargeable) {
                $order = PosOrder::create([
                    'order_number'    => PosOrder::generateOrderNumber(),
                    'employee_id'     => $data['employee_id'],
                    'admin_id'        => $admin->id,
                    'subtotal'        => $subtotal,
                    'tip'             => 0,
                    'card_fee'        => $cardFee,
                    'total'           => $chargeable,
                    'amount_cents'    => $amountCents,
                    'currency'        => config('nexo_pos.plutopay.currency', 'usd'),
                    'payment_method'  => 'card',
                    'status'          => 'awaiting_payment',
                    'idempotency_key' => $data['idempotency_key'],
                    'customer_email'  => $data['customer_email'] ?? null,
                    'notes'           => $data['notes'] ?? null,
                ]);

                foreach ($data['items'] as $item) {
                    PosOrderItem::create([
                        'pos_order_id' => $order->id,
                        'service_id'   => $item['service_id'] ?? null,
                        'name'         => $item['name'],
                        'price'        => $item['price'],
                        'quantity'     => $item['quantity'] ?? 1,
                        'is_custom'    => $item['is_custom'] ?? empty($item['service_id']),
                    ]);
                }

                return $order;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Lost the race on the unique key — reuse the winner's row.
            $order = PosOrder::where('idempotency_key', $data['idempotency_key'])->firstOrFail();
        }

        return $this->driveAndRespond($order, $data['reader_id'] ?? null);
    }

    /**
     * Drive PlutoPay for a row that still needs the reader, then reply with its
     * current status. Safe to re-enter (Idempotency-Key on create-payment).
     */
    private function driveAndRespond(PosOrder $order, ?string $readerId = null)
    {
        if ($order->isTerminal()) {
            return $this->statusPayload($order);
        }

        // Already handed to the reader — don't re-trigger.
        if ($order->status === 'processing' && $order->payment_intent_id) {
            return $this->statusPayload($order);
        }

        try {
            if (!$order->payment_intent_id) {
                $payment = $this->pluto()->createPayment(
                    (int) $order->amount_cents,
                    'nexo-app-' . $order->idempotency_key,
                    ['pos_order_id' => $order->id],
                );

                $order->update([
                    'payment_intent_id'   => $payment['payment_intent_id'],
                    'provider_payment_id' => $payment['provider_id'],
                    'reference'           => $payment['reference'],
                ]);
            }

            $this->pluto()->processPayment($order->payment_intent_id, $readerId);
            $order->update(['status' => 'processing']);
        } catch (PlutoPayException $e) {
            Log::warning('NexoPos PlutoPay start failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success'  => false,
                'order_id' => $order->id,
                'status'   => $order->status,
                'message'  => 'Could not start the card reader: ' . $e->getMessage(),
            ], 502);
        }

        return $this->statusPayload($order);
    }

    public function status(Request $request, $id)
    {
        $order = PosOrder::findOrFail($id);
        $admin = $request->attributes->get('pos_admin');

        if ($order->admin_id !== $admin->id) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return $this->statusPayload($order);
    }

    /**
     * SANDBOX ONLY. Fires the simulate-payment webhook so the reader flow
     * settles without a real card. Guarded server-side by the client's
     * TEST-MODE constructor guard.
     */
    public function simulate(Request $request, $id)
    {
        $data = $request->validate([
            'reader_id' => 'nullable|string|max:128',
        ]);

        $order = PosOrder::findOrFail($id);
        $admin = $request->attributes->get('pos_admin');

        if ($order->admin_id !== $admin->id) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        try {
            $this->pluto()->simulatePayment($data['reader_id'] ?? null);
        } catch (PlutoPayException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    private function statusPayload(PosOrder $order)
    {
        return response()->json([
            'success'        => true,
            'order_id'       => $order->id,
            'order_number'   => $order->order_number,
            'status'         => $order->status,   // awaiting_payment|processing|completed|failed|canceled
            'paid'           => $order->status === 'completed',
            'failure_reason' => $order->failure_reason,
            'subtotal'       => (float) $order->subtotal,
            'card_fee'       => (float) $order->card_fee,
            'tip'            => (float) $order->tip,
            'total'          => (float) $order->total,
        ]);
    }
}
