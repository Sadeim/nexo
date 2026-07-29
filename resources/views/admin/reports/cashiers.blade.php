@extends('admin.layouts.master', ['is_active_parent' => 'reports', 'is_active' => 'cashiers_report'])
@section('title', 'Cashiers report')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="page-content-header mb-5 d-flex justify-content-between align-items-center">
                    <h2 class="table-title">Cashiers</h2>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light">&larr; Reports</a>
                </div>

                @include('admin.reports._filter', ['route' => 'admin.reports.cashiers'])

                <div class="card card-flush">
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th>Cashier</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Cash</th>
                                        <th class="text-end">Zelle</th>
                                        <th class="text-end">Card</th>
                                        <th class="text-end">Fees</th>
                                        <th class="text-end">Tips</th>
                                        <th class="text-end">Gross</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $r)
                                        <tr>
                                            <td class="fw-bold">
                                                {{ $r['name'] }}
                                                @if ($r['email'])
                                                    <div class="text-muted fs-8">{{ $r['email'] }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $r['orders'] }}</td>
                                            <td class="text-end">${{ number_format($r['cash'], 2) }}</td>
                                            <td class="text-end">${{ number_format($r['zelle'], 2) }}</td>
                                            <td class="text-end">${{ number_format($r['card'], 2) }}</td>
                                            <td class="text-end {{ $r['fees'] > 0 ? 'text-primary' : 'text-muted' }}">
                                                {{ $r['fees'] > 0 ? '+$' . number_format($r['fees'], 2) : '—' }}
                                            </td>
                                            <td class="text-end text-success">${{ number_format($r['tips'], 2) }}</td>
                                            <td class="text-end fw-bold">${{ number_format($r['gross'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-10">No sales in this range.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold border-top">
                                        <td>TOTAL</td>
                                        <td class="text-center">{{ $totals['orders'] }}</td>
                                        <td class="text-end">${{ number_format($totals['cash'], 2) }}</td>
                                        <td class="text-end">${{ number_format($totals['zelle'], 2) }}</td>
                                        <td class="text-end">${{ number_format($totals['card'], 2) }}</td>
                                        <td class="text-end text-primary">
                                            {{ $totals['fees'] > 0 ? '+$' . number_format($totals['fees'], 2) : '—' }}
                                        </td>
                                        <td class="text-end text-success">${{ number_format($totals['tips'], 2) }}</td>
                                        <td class="text-end">${{ number_format($totals['gross'], 2) }}</td>
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
