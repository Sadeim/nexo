<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreTransactionRequest;
use App\Models\PosTransaction;
use App\Models\Service;
use App\Services\Pos\CartCalculator;
use App\Services\Pos\Exceptions\InvalidPriceException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct(private CartCalculator $calculator)
    {
    }

    /**
     * Persist a POS sale.
     *
     * The server is the single source of truth: it re-resolves every price
     * from the database, re-validates any custom price, and recomputes the
     * totals with CartCalculator. Any subtotal/total sent by the browser is
     * ignored entirely.
     */
    public function store(StoreTransactionRequest $request)
    {
        $paymentMethod = $request->input('payment_method');

        // Card is structural-only for now (button disabled in the UI). Reject
        // it defensively here; the schema is already card-ready for later.
        if ($paymentMethod === 'card') {
            return response()->json([
                'success' => false,
                'message' => 'Card payments are not available yet.',
            ], 422);
        }

        $inputItems = $request->input('items', []);

        // 1) Resolve services and build authoritative cart lines.
        $serviceIds = collect($inputItems)->pluck('service_id')->unique()->all();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        $lines = [];
        try {
            foreach ($inputItems as $item) {
                $service = $services->get($item['service_id']);

                if (!$service) {
                    return response()->json([
                        'success' => false,
                        'message' => 'One of the selected services no longer exists.',
                    ], 422);
                }

                // NULL-priced services are never sellable.
                if (!$service->isSellable()) {
                    return response()->json([
                        'success' => false,
                        'message' => "\"{$service->name}\" has no price and cannot be sold.",
                    ], 422);
                }

                // Custom price overrides the stored price for THIS sale only.
                // An explicitly provided custom price may be 0 (comp/free);
                // when absent we fall back to the stored (strictly positive)
                // price.
                $customPrice = $item['custom_price'] ?? null;
                $hasCustom = $customPrice !== null && $customPrice !== '';
                $unitPrice = $hasCustom ? $customPrice : (string) $service->price;

                $lines[] = [
                    'service_id'     => $service->id,
                    'service_name'   => $service->name,   // snapshot
                    'original_price' => (string) $service->price, // snapshot (never changes)
                    'unit_price'     => $unitPrice,
                    'quantity'       => $item['quantity'],
                    // Only a deliberate custom price is allowed to be zero.
                    'allow_zero'     => $hasCustom,
                ];
            }

            // 2) Recompute totals authoritatively (validates prices/quantities).
            $cart = $this->calculator->calculate($lines);
        } catch (InvalidPriceException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        // 3) Persist atomically. Employee = the logged-in cashier (no picker).
        $transaction = DB::transaction(function () use ($cart, $paymentMethod) {
            $transaction = PosTransaction::create([
                'admin_id'       => Auth::guard('pos')->id(),
                'subtotal'       => $cart['subtotal'],
                'total'          => $cart['total'],
                'payment_method' => $paymentMethod,
                'status'         => 'completed', // cash completes immediately
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

        return response()->json([
            'success'        => true,
            'message'        => 'Sale completed.',
            'transaction_id' => $transaction->id,
            'subtotal'       => $cart['subtotal'],
            'total'          => $cart['total'],
        ], 201);
    }
}
