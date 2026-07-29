<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PosOrderResource;
use App\Models\Employee;
use App\Models\PosOrder;
use Illuminate\Http\Request;

/**
 * Read-only view of the Flutter POS mobile-app orders.
 * Distinct from PosTransactionController, which shows the Web POS.
 */
class PosOrderController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);
        return view('admin.pos_orders.index', compact('employees'));
    }

    public function datatable(Request $request)
    {
        // Settled only — a card sale that never cleared is not a sale.
        $items = PosOrder::query()->settled()->with(['employee', 'admin']);

        if ($request->filled('employee_id')) {
            $items->where('employee_id', $request->employee_id);
        }
        if ($request->filled('payment_method')) {
            $items->where('payment_method', $request->payment_method);
        }
        if ($request->filled('date_from')) {
            $items->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $items->whereDate('created_at', '<=', $request->date_to);
        }

        $items->orderBy('id', 'DESC');

        return $this->filterDataTable($items, $request, null, PosOrderResource::class);
    }

    public function show($id)
    {
        $order = PosOrder::with(['items.service', 'employee', 'admin'])->findOrFail($id);
        return view('admin.pos_orders.show', compact('order'));
    }
}
