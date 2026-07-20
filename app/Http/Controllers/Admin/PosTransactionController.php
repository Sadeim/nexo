<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PosTransactionResource;
use App\Models\Admin;
use App\Models\PosTransaction;
use Illuminate\Http\Request;

/**
 * Read-only view of the POS financial log. Transactions and their line items
 * are never editable here — this is an audit/reporting screen only.
 */
class PosTransactionController extends Controller
{
    public function index()
    {
        // Cashiers/admins who have ever made a sale, for the employee filter.
        $employees = Admin::whereHas('posTransactions')->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.pos_transactions.index', compact('employees'));
    }

    public function datatable(Request $request)
    {
        $items = PosTransaction::query()->with('admin');

        // Filter: employee
        if ($request->filled('admin_id')) {
            $items->where('admin_id', $request->admin_id);
        }

        // Filter: date range (inclusive) on created_at
        if ($request->filled('date_from')) {
            $items->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $items->whereDate('created_at', '<=', $request->date_to);
        }

        $items->orderBy('id', 'DESC');

        return $this->filterDataTable($items, $request, null, PosTransactionResource::class);
    }

    public function show($id)
    {
        $transaction = PosTransaction::with(['admin', 'items'])->findOrFail($id);
        return view('admin.pos_transactions.show', compact('transaction'));
    }
}
