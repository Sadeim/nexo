<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeePayment;
use App\Models\PosOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * "Who do I owe how much" — per-employee report over a date range, plus the
 * ledger of payouts (record + list + delete).
 *
 * Model:
 *   subtotal_earned = SUM(pos_orders.subtotal WHERE status=completed)
 *   tips_earned     = SUM(pos_orders.tip)
 *   commission      = subtotal_earned * (employee.commission_rate / 100)
 *   total_earned    = commission + tips_earned
 *   paid_in_range   = SUM(employee_payments.amount WHERE paid_at IN range)
 *   balance         = total_earned - paid_in_range
 *
 * Only orders from the mobile Flutter POS count here (pos_orders). Web POS
 * (pos_transactions) is not attributed to an employee, so it isn't summed.
 */
class EmployeeReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->parseRange($request);

        $employees = Employee::orderBy('sort_order')->orderBy('name')->get();

        // Convert the caller-timezone range to UTC once so every sub-query on
        // this page uses the same, correct boundaries.
        $fromUtc = $from->copy()->startOfDay()->utc();
        $toUtc   = $to->copy()->endOfDay()->utc();

        $rows = $employees->map(function (Employee $employee) use ($fromUtc, $toUtc) {
            $orders = PosOrder::where('employee_id', $employee->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$fromUtc, $toUtc])
                ->get();

            $cashOrders  = $orders->where('payment_method', 'cash');
            $cardOrders  = $orders->where('payment_method', 'card');
            $zelleOrders = $orders->where('payment_method', 'zelle');

            $subtotalSum = (float) $orders->sum('subtotal');
            $tipSum      = (float) $orders->sum('tip');
            $commission  = $employee->commissionOn($subtotalSum);
            $earned      = round($commission + $tipSum, 2);

            $paid = (float) EmployeePayment::where('employee_id', $employee->id)
                ->whereBetween('paid_at', [$fromUtc, $toUtc])
                ->sum('amount');

            return [
                'employee'        => $employee,
                'orders_count'    => $orders->count(),
                'subtotal'        => $subtotalSum,
                'tips'            => $tipSum,
                // How the money actually came in — what's physically in the
                // drawer vs what settled to the bank.
                'cash_total'      => (float) $cashOrders->sum('total'),
                'cash_tips'       => (float) $cashOrders->sum('tip'),
                'zelle_total'     => (float) $zelleOrders->sum('total'),
                'zelle_tips'      => (float) $zelleOrders->sum('tip'),
                'card_total'      => (float) $cardOrders->sum('total'),
                'card_tips'       => (float) $cardOrders->sum('tip'),
                'commission_rate' => (float) $employee->commission_rate,
                'commission'      => $commission,
                'earned'          => $earned,
                'paid'            => $paid,
                'balance'         => round($earned - $paid, 2),
            ];
        });

        $totals = [
            'subtotal'   => (float) $rows->sum('subtotal'),
            'tips'       => (float) $rows->sum('tips'),
            'cash_total'  => (float) $rows->sum('cash_total'),
            'cash_tips'   => (float) $rows->sum('cash_tips'),
            'zelle_total' => (float) $rows->sum('zelle_total'),
            'zelle_tips'  => (float) $rows->sum('zelle_tips'),
            'card_total'  => (float) $rows->sum('card_total'),
            'card_tips'   => (float) $rows->sum('card_tips'),
            'commission' => (float) $rows->sum('commission'),
            'earned'     => (float) $rows->sum('earned'),
            'paid'       => (float) $rows->sum('paid'),
            'balance'    => (float) $rows->sum('balance'),
        ];

        return view('admin.reports.employees', compact('rows', 'totals', 'from', 'to'));
    }

    public function pay(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount'      => 'required|numeric|min:0.01',
            'period_from' => 'nullable|date',
            'period_to'   => 'nullable|date|after_or_equal:period_from',
            'notes'       => 'nullable|string|max:1000',
        ]);

        EmployeePayment::create([
            'employee_id' => $data['employee_id'],
            'admin_id'    => Auth::guard('admin')->id(),
            'amount'      => $data['amount'],
            'period_from' => $data['period_from'] ?? null,
            'period_to'   => $data['period_to'] ?? null,
            'paid_at'     => now(),
            'notes'       => $data['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function history($employeeId, Request $request)
    {
        $employee = Employee::findOrFail($employeeId);
        [$from, $to] = $this->parseRange($request, defaultDays: 90);

        $payments = EmployeePayment::with('admin')
            ->where('employee_id', $employee->id)
            ->whereBetween('paid_at', [
                $from->copy()->startOfDay()->utc(),
                $to->copy()->endOfDay()->utc(),
            ])
            ->orderBy('paid_at', 'desc')
            ->get();

        $total = (float) $payments->sum('amount');

        return view('admin.reports.employee_history', compact('employee', 'payments', 'from', 'to', 'total'));
    }

    public function destroyPayment($id)
    {
        $payment = EmployeePayment::findOrFail($id);
        $payment->delete();
        return redirect()->back()->with('success', 'Payment deleted.');
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
