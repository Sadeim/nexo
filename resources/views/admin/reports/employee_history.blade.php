@extends('admin.layouts.master', ['is_active_parent' => 'reports', 'is_active' => 'employees_report'])
@section('title', $employee->name . ' — payment history')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="page-content-header mb-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="table-title">
                            Payment history — <span class="text-primary">{{ $employee->name }}</span>
                        </h2>
                        <a href="{{ route('admin.reports.employees.index') }}" class="btn btn-light">&larr; Back to report</a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Filter --}}
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
                            <div class="col-md-6 d-flex gap-2">
                                <button class="btn btn-sm btn-primary">Apply</button>
                                <a href="{{ route('admin.reports.employees.history', $employee->id) }}" class="btn btn-sm btn-light">Last 90 days</a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="card card-flush">
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th>Date</th>
                                        <th>Period</th>
                                        <th class="text-end">Amount</th>
                                        <th>Notes</th>
                                        <th>By</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $p)
                                        <tr>
                                            <td>{{ $p->paid_at->format('Y-m-d H:i') }}</td>
                                            <td class="text-muted">
                                                @if ($p->period_from || $p->period_to)
                                                    {{ optional($p->period_from)->format('M j') }}
                                                    &rarr;
                                                    {{ optional($p->period_to)->format('M j') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">${{ number_format((float) $p->amount, 2) }}</td>
                                            <td class="text-muted">{{ $p->notes }}</td>
                                            <td class="text-muted">{{ $p->admin->name ?? '—' }}</td>
                                            <td class="text-center">
                                                <form method="POST"
                                                      action="{{ route('admin.reports.employees.pay.destroy', $p->id) }}"
                                                      onsubmit="return confirm('Delete this payment record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-10">No payments in this range.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold border-top">
                                        <td colspan="2" class="text-end">TOTAL</td>
                                        <td class="text-end">${{ number_format($total, 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
