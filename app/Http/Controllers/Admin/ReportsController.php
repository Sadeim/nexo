<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Aggregate reports for the Flutter POS. Scope is strictly `pos_orders`
 * (mobile app); the browser Web POS lives in a separate table and is not
 * mixed in here to keep numbers unambiguous.
 *
 * Time boundary: `pos_orders.created_at` in the app timezone. Only orders
 * with status = 'completed' count.
 */
class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    // ---------- Sales ----------

    public function sales(Request $request)
    {
        [$from, $to] = $this->parseRange($request, defaultDays: 29);

        $orders = PosOrder::completedIn($from, $to)->get();

        $totals = [
            'orders'     => $orders->count(),
            'gross'      => (float) $orders->sum('total'),
            'subtotal'   => (float) $orders->sum('subtotal'),
            'tips'       => (float) $orders->sum('tip'),
            'cash'        => (float) $orders->where('payment_method', 'cash')->sum('total'),
            'card'        => (float) $orders->where('payment_method', 'card')->sum('total'),
            'zelle'       => (float) $orders->where('payment_method', 'zelle')->sum('total'),
            'cash_count'  => $orders->where('payment_method', 'cash')->count(),
            'card_count'  => $orders->where('payment_method', 'card')->count(),
            'zelle_count' => $orders->where('payment_method', 'zelle')->count(),
            'avg_ticket' => $orders->count() > 0
                ? round($orders->sum('total') / $orders->count(), 2)
                : 0.0,
        ];

        // Per-day breakdown
        $daily = $orders
            ->groupBy(fn($o) => $o->created_at->format('Y-m-d'))
            ->map(function ($dayOrders, $date) {
                return [
                    'date'       => $date,
                    'orders'     => $dayOrders->count(),
                    'subtotal'   => (float) $dayOrders->sum('subtotal'),
                    'tips'       => (float) $dayOrders->sum('tip'),
                    'total'      => (float) $dayOrders->sum('total'),
                    'cash'       => (float) $dayOrders->where('payment_method', 'cash')->sum('total'),
                    'zelle'      => (float) $dayOrders->where('payment_method', 'zelle')->sum('total'),
                    'card'       => (float) $dayOrders->where('payment_method', 'card')->sum('total'),
                ];
            })
            ->sortByDesc('date')
            ->values();

        return view('admin.reports.sales', compact('from', 'to', 'totals', 'daily'));
    }

    // ---------- Services ----------

    public function services(Request $request)
    {
        [$from, $to] = $this->parseRange($request, defaultDays: 29);

        $rows = PosOrderItem::query()
            ->select([
                'name',
                'is_custom',
                DB::raw('SUM(quantity) as qty'),
                DB::raw('SUM(price * quantity) as revenue'),
                DB::raw('COUNT(*) as line_count'),
            ])
            ->whereHas('posOrder', function ($q) use ($from, $to) {
                $q->completedIn($from, $to);
            })
            ->groupBy('name', 'is_custom')
            ->orderByDesc('revenue')
            ->get();

        $totals = [
            'items'   => (int) $rows->sum('qty'),
            'revenue' => (float) $rows->sum('revenue'),
            'lines'   => (int) $rows->sum('line_count'),
        ];

        return view('admin.reports.services', compact('from', 'to', 'rows', 'totals'));
    }

    // ---------- Cashiers ----------

    public function cashiers(Request $request)
    {
        [$from, $to] = $this->parseRange($request, defaultDays: 29);

        $rows = PosOrder::query()
            ->completedIn($from, $to)
            ->with('admin:id,name,email')
            ->get()
            ->groupBy('admin_id')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'admin_id' => $first->admin_id,
                    'name'     => $first->admin->name ?? '—',
                    'email'    => $first->admin->email ?? '',
                    'orders'   => $group->count(),
                    'gross'    => (float) $group->sum('total'),
                    'cash'     => (float) $group->where('payment_method', 'cash')->sum('total'),
                    'zelle'    => (float) $group->where('payment_method', 'zelle')->sum('total'),
                    'card'     => (float) $group->where('payment_method', 'card')->sum('total'),
                    'tips'     => (float) $group->sum('tip'),
                ];
            })
            ->sortByDesc('gross')
            ->values();

        $totals = [
            'orders' => (int) $rows->sum('orders'),
            'gross'  => (float) $rows->sum('gross'),
            'cash'   => (float) $rows->sum('cash'),
            'zelle'  => (float) $rows->sum('zelle'),
            'card'   => (float) $rows->sum('card'),
            'tips'   => (float) $rows->sum('tips'),
        ];

        return view('admin.reports.cashiers', compact('from', 'to', 'rows', 'totals'));
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
