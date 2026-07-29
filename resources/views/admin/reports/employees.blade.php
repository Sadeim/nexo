@extends('admin.layouts.master', ['is_active_parent' => 'reports', 'is_active' => 'employees_report'])
@section('title', 'Employee earnings report')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="page-content-header mb-5">
                    <h2 class="table-title">Employee Earnings</h2>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Date range + quick presets --}}
                <div class="card card-flush mb-5">
                    <div class="card-body py-5">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="fs-7 fw-semibold mb-2">From</label>
                                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-7 fw-semibold mb-2">To</label>
                                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-primary">Apply</button>
                                <a href="{{ route('admin.reports.employees.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-light">Today</a>
                                <a href="{{ route('admin.reports.employees.index', ['from' => now()->subDay()->toDateString(), 'to' => now()->subDay()->toDateString()]) }}" class="btn btn-sm btn-light">Yesterday</a>
                                <a href="{{ route('admin.reports.employees.index', ['from' => now()->startOfWeek()->toDateString(), 'to' => now()->endOfWeek()->toDateString()]) }}" class="btn btn-sm btn-light">This week</a>
                                <a href="{{ route('admin.reports.employees.index', ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->endOfMonth()->toDateString()]) }}" class="btn btn-sm btn-light">This month</a>
                            </div>
                        </form>
                        <div class="text-muted fs-8 mt-3">
                            Showing sales from <strong>{{ $from->format('D, M j Y') }}</strong> to <strong>{{ $to->format('D, M j Y') }}</strong>
                            &nbsp;·&nbsp; Payments in the same range count against the balance.
                        </div>
                    </div>
                </div>

                {{-- Report table --}}
                <div class="card card-flush">
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th>Employee</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Sales</th>
                                        <th class="text-end">Tips</th>
                                        <th class="text-end bg-light-success">Cash in</th>
                                        <th class="text-end bg-light-warning">Zelle in</th>
                                        <th class="text-end bg-light-info">Card in</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-end">Commission</th>
                                        <th class="text-end">Earned</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Balance</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $r)
                                        <tr>
                                            <td class="fw-bold">{{ $r['employee']->name }}</td>
                                            <td class="text-center">{{ $r['orders_count'] }}</td>
                                            <td class="text-end">${{ number_format($r['subtotal'], 2) }}</td>
                                            <td class="text-end text-success">${{ number_format($r['tips'], 2) }}</td>
                                            <td class="text-end bg-light-success">
                                                ${{ number_format($r['cash_total'], 2) }}
                                                @if ($r['cash_tips'] > 0)
                                                    <div class="text-muted fs-8">incl. tip ${{ number_format($r['cash_tips'], 2) }}</div>
                                                @endif
                                            </td>
                                            <td class="text-end bg-light-warning">
                                                ${{ number_format($r['zelle_total'], 2) }}
                                                @if ($r['zelle_tips'] > 0)
                                                    <div class="text-muted fs-8">incl. tip ${{ number_format($r['zelle_tips'], 2) }}</div>
                                                @endif
                                            </td>
                                            <td class="text-end bg-light-info">
                                                ${{ number_format($r['card_total'], 2) }}
                                                @if ($r['card_tips'] > 0)
                                                    <div class="text-muted fs-8">incl. tip ${{ number_format($r['card_tips'], 2) }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($r['commission_rate'], 2) }}%
                                                @if ($r['commission_rate'] == 0)
                                                    <a href="{{ route('admin.employees.edit', $r['employee']->id) }}"
                                                       class="d-block fs-8 text-primary" title="Set a commission rate">set</a>
                                                @endif
                                            </td>
                                            <td class="text-end">${{ number_format($r['commission'], 2) }}</td>
                                            <td class="text-end fw-bold">${{ number_format($r['earned'], 2) }}</td>
                                            <td class="text-end">${{ number_format($r['paid'], 2) }}</td>
                                            <td class="text-end fw-bold {{ $r['balance'] > 0 ? 'text-danger' : 'text-muted' }}">
                                                ${{ number_format($r['balance'], 2) }}
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#payModal"
                                                        data-employee-id="{{ $r['employee']->id }}"
                                                        data-employee-name="{{ $r['employee']->name }}"
                                                        data-balance="{{ number_format($r['balance'], 2, '.', '') }}"
                                                        data-from="{{ $from->format('Y-m-d') }}"
                                                        data-to="{{ $to->format('Y-m-d') }}">
                                                        <i class="fa fa-dollar-sign me-1"></i> Pay
                                                    </button>
                                                    <a class="btn btn-sm btn-light"
                                                        href="{{ route('admin.reports.employees.history', $r['employee']->id) }}">
                                                        History
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="13" class="text-center text-muted py-10">No employees.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold border-top">
                                        <td>TOTAL</td>
                                        <td></td>
                                        <td class="text-end">${{ number_format($totals['subtotal'], 2) }}</td>
                                        <td class="text-end text-success">${{ number_format($totals['tips'], 2) }}</td>
                                        <td class="text-end bg-light-success">${{ number_format($totals['cash_total'], 2) }}</td>
                                        <td class="text-end bg-light-warning">${{ number_format($totals['zelle_total'], 2) }}</td>
                                        <td class="text-end bg-light-info">${{ number_format($totals['card_total'], 2) }}</td>
                                        <td></td>
                                        <td class="text-end">${{ number_format($totals['commission'], 2) }}</td>
                                        <td class="text-end">${{ number_format($totals['earned'], 2) }}</td>
                                        <td class="text-end">${{ number_format($totals['paid'], 2) }}</td>
                                        <td class="text-end {{ $totals['balance'] > 0 ? 'text-danger' : 'text-muted' }}">
                                            ${{ number_format($totals['balance'], 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pay modal --}}
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.reports.employees.pay') }}" class="modal-content">
                @csrf
                <input type="hidden" name="employee_id" id="pm_employee_id">
                <div class="modal-header">
                    <h5 class="modal-title">Pay <span id="pm_employee_name" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="required fs-6 fw-semibold mb-2">Amount ($)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="pm_amount" class="form-control" required>
                        <div class="form-text">Current balance for the selected range: <strong id="pm_balance_hint">$0.00</strong></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="fs-7 fw-semibold mb-2">Period from (optional)</label>
                            <input type="date" name="period_from" id="pm_period_from" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="fs-7 fw-semibold mb-2">Period to (optional)</label>
                            <input type="date" name="period_to" id="pm_period_to" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="fs-6 fw-semibold mb-2">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Cash, Zelle #123, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('payModal').addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            document.getElementById('pm_employee_id').value = btn.dataset.employeeId;
            document.getElementById('pm_employee_name').textContent = btn.dataset.employeeName;
            document.getElementById('pm_balance_hint').textContent = '$' + btn.dataset.balance;
            document.getElementById('pm_amount').value = btn.dataset.balance;
            document.getElementById('pm_period_from').value = btn.dataset.from;
            document.getElementById('pm_period_to').value = btn.dataset.to;
        });
    </script>
@endpush
