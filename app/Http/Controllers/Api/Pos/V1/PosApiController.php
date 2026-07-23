<?php

namespace App\Http\Controllers\Api\Pos\V1;

use App\Http\Controllers\Controller;
use App\Mail\PosReceiptMail;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\PosApiToken;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * REST API for the Flutter POS mobile terminal.
 * Auth: bearer token issued by /login, verified by `pos.api` middleware.
 */
class PosApiController extends Controller
{
    // ---------- Public ----------

    public function health()
    {
        return response()->json([
            'success' => true,
            'app' => 'nexo-pos',
            'time' => now()->toIso8601String(),
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $admin = Admin::where('email', $data['email'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = PosApiToken::issue($admin, $data['device_name'] ?? 'POS Terminal');

        return response()->json([
            'success' => true,
            'token' => $token->token,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    /**
     * Advertised app version for OTA self-update. Values are read from
     * config/nexo_pos.php (env-driven) so bumping is a `.env` edit +
     * `php artisan config:cache` on the server — no code change.
     */
    public function appVersion()
    {
        return response()->json([
            'success' => true,
            'version_code'  => (int) config('nexo_pos.version_code'),
            'version_name'  => (string) config('nexo_pos.version_name'),
            'apk_url'       => config('nexo_pos.apk_url'),
            'mandatory'     => (bool) config('nexo_pos.mandatory'),
            'release_notes' => (string) config('nexo_pos.release_notes'),
        ]);
    }

    // ---------- Authenticated ----------

    public function me(Request $request)
    {
        $admin = $request->attributes->get('pos_admin');

        return response()->json([
            'success' => true,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->attributes->get('pos_token');
        if ($token) {
            $token->delete();
        }

        return response()->json(['success' => true]);
    }

    public function employees()
    {
        $employees = Employee::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'avatar']);

        return response()->json([
            'success' => true,
            'employees' => $employees,
        ]);
    }

    public function services()
    {
        $services = Service::query()
            ->whereNotNull('price')
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->where('is_active', true)],
            'payment_method' => 'required|in:cash,card',
            'tip' => 'nullable|numeric|min:0',
            'customer_email' => 'nullable|email',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.is_custom' => 'nullable|boolean',
        ]);

        $admin = $request->attributes->get('pos_admin');

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += ((float) $item['price']) * ($item['quantity'] ?? 1);
        }
        $tip = (float) ($data['tip'] ?? 0);
        $total = round($subtotal + $tip, 2);

        try {
            $order = DB::transaction(function () use ($data, $admin, $subtotal, $tip, $total) {
                $order = PosOrder::create([
                    'order_number' => PosOrder::generateOrderNumber(),
                    'employee_id' => $data['employee_id'],
                    'admin_id' => $admin->id,
                    'subtotal' => round($subtotal, 2),
                    'tip' => $tip,
                    'total' => $total,
                    'payment_method' => $data['payment_method'],
                    'customer_email' => $data['customer_email'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($data['items'] as $item) {
                    PosOrderItem::create([
                        'pos_order_id' => $order->id,
                        'service_id' => $item['service_id'] ?? null,
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'] ?? 1,
                        'is_custom' => $item['is_custom'] ?? empty($item['service_id']),
                    ]);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            Log::error('POS order create failed', ['err' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save order. Please try again.',
            ], 500);
        }

        if (!empty($order->customer_email)) {
            $this->sendReceiptMail($order->fresh(['items', 'employee']));
        }

        return response()->json([
            'success' => true,
            'order' => $this->serializeOrder($order->fresh(['items', 'employee'])),
        ], 201);
    }

    public function showOrder($id)
    {
        $order = PosOrder::with(['items', 'employee'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => $this->serializeOrder($order),
        ]);
    }

    public function emailReceipt(Request $request, $id)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $order = PosOrder::with(['items', 'employee'])->findOrFail($id);
        $order->forceFill(['customer_email' => $data['email']])->save();

        $ok = $this->sendReceiptMail($order);

        return response()->json([
            'success' => $ok,
            'message' => $ok ? 'Receipt sent' : 'Failed to send email',
        ], $ok ? 200 : 500);
    }

    // ---------- Helpers ----------

    protected function sendReceiptMail(PosOrder $order): bool
    {
        try {
            Mail::to($order->customer_email)->send(new PosReceiptMail($order));
            $order->forceFill(['receipt_sent_at' => now()])->save();
            return true;
        } catch (\Throwable $e) {
            Log::warning('POS receipt email failed', [
                'order_id' => $order->id,
                'err' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function serializeOrder(PosOrder $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'employee' => $order->employee ? [
                'id' => $order->employee->id,
                'name' => $order->employee->name,
            ] : null,
            'subtotal' => (float) $order->subtotal,
            'tip' => (float) $order->tip,
            'total' => (float) $order->total,
            'payment_method' => $order->payment_method,
            'customer_email' => $order->customer_email,
            'receipt_sent_at' => optional($order->receipt_sent_at)->toIso8601String(),
            'notes' => $order->notes,
            'created_at' => $order->created_at->toIso8601String(),
            'items' => $order->items->map(fn($it) => [
                'id' => $it->id,
                'service_id' => $it->service_id,
                'name' => $it->name,
                'price' => (float) $it->price,
                'quantity' => (int) $it->quantity,
                'is_custom' => (bool) $it->is_custom,
                'line_total' => (float) $it->price * (int) $it->quantity,
            ])->all(),
        ];
    }
}
