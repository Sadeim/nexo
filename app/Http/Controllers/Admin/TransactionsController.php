<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\PosOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * One page showing every field of every POS sale — the full ledger, as
 * opposed to the trimmed "POS Orders" list.
 *
 * Scope is `pos_orders` (the tablet POS). The browser-based Web POS writes to
 * a separate `pos_transactions` table with a different shape and no employee
 * attribution, so mixing the two here would produce numbers that don't
 * reconcile; it keeps its own screen.
 *
 * Only SETTLED orders are listed. A card sale that is still awaiting_payment /
 * processing, or that ended failed / canceled, never moved money and must not
 * show up in any list or total.
 */
class TransactionsController extends Controller
{
    /** Rows per page. */
    private const PER_PAGE = 50;

    public function index(Request $request)
    {
        [$from, $to] = $this->parseRange($request, defaultDays: 29);

        $query = PosOrder::query()
            ->settled()
            ->with(['items', 'employee', 'admin'])
            ->whereBetween('created_at', [
                $from->copy()->startOfDay()->utc(),
                $to->copy()->endOfDay()->utc(),
            ]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('q')) {
            $term = trim($request->q);
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'LIKE', "%{$term}%")
                  ->orWhere('customer_email', 'LIKE', "%{$term}%")
                  ->orWhere('reference', 'LIKE', "%{$term}%")
                  ->orWhere('payment_intent_id', 'LIKE', "%{$term}%");
            });
        }

        // Totals cover the WHOLE filtered set, not just the visible page.
        // Standalone tips never reached the drawer, so they're summed apart
        // from shop takings rather than folded into them.
        $sums = (clone $query)
            ->selectRaw('
                COUNT(*)                                                       AS rows_all,
                COALESCE(SUM(CASE WHEN is_tip_only = 0 THEN 1 ELSE 0 END), 0)  AS orders,
                COALESCE(SUM(CASE WHEN is_tip_only = 0 THEN subtotal END), 0)      AS subtotal,
                COALESCE(SUM(CASE WHEN is_tip_only = 0 THEN card_fee END), 0)      AS card_fee,
                COALESCE(SUM(CASE WHEN is_tip_only = 0 THEN tip_remainder END), 0) AS tip_remainder,
                COALESCE(SUM(CASE WHEN is_tip_only = 0 THEN tip END), 0)           AS tip,
                COALESCE(SUM(CASE WHEN is_tip_only = 0 THEN total END), 0)         AS total,
                COALESCE(SUM(CASE WHEN is_tip_only = 1 THEN tip END), 0)           AS direct_tips
            ')
            ->first();

        $totals = [
            'orders'      => (int) ($sums->orders ?? 0),
            'subtotal'    => (float) ($sums->subtotal ?? 0),
            'fees'        => (float) ($sums->card_fee ?? 0) + (float) ($sums->tip_remainder ?? 0),
            'tips'        => (float) ($sums->tip ?? 0),
            'direct_tips' => (float) ($sums->direct_tips ?? 0),
            'total'       => (float) ($sums->total ?? 0),
        ];

        $orders = $query->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.transactions.index', [
            'orders'    => $orders,
            'totals'    => $totals,
            'from'      => $from,
            'to'        => $to,
            'employees' => Employee::orderBy('name')->get(['id', 'name']),
            'cashiers'  => Admin::orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function parseRange(Request $request, int $defaultDays = 0): array
    {
        $tz = config('app.timezone');

        $from = $request->filled('from')
            ? Carbon::parse($request->from, $tz)
            : Carbon::now($tz)->subDays($defaultDays);

        $to = $request->filled('to')
            ? Carbon::parse($request->to, $tz)
            : Carbon::now($tz);

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }
}
