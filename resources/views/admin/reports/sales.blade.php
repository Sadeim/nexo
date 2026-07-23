@extends('admin.layouts.master', ['is_active_parent' => 'reports', 'is_active' => 'sales_report'])
@section('title', 'Sales report')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="page-content-header mb-5 d-flex justify-content-between align-items-center">
                    <h2 class="table-title">Sales overview</h2>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light">&larr; Reports</a>
                </div>

                @include('admin.reports._filter', ['route' => 'admin.reports.sales'])

                {{-- KPI cards --}}
                <div class="row g-4 mb-5">
                    @php
                        $kpi = function ($label, $value, $sub = null, $color = 'primary') {
                            return [
                                'label' => $label,
                                'value' => $value,
                                'sub'   => $sub,
                                'color' => $color,
                            ];
                        };
                        $kpis = [
                            $kpi('Orders',       $totals['orders']),
                            $kpi('Gross',        '$' . number_format($totals['gross'], 2), 'Subtotal $' . number_format($totals['subtotal'], 2) . ' + Tips $' . number_format($totals['tips'], 2)),
                            $kpi('Cash',         '$' . number_format($totals['cash'], 2), $totals['cash_count'] . ' orders', 'success'),
                            $kpi('Card',         '$' . number_format($totals['card'], 2), $totals['card_count'] . ' orders', 'info'),
                            $kpi('Avg. ticket',  '$' . number_format($totals['avg_ticket'], 2), null, 'warning'),
                        ];
                    @endphp
                    @foreach ($kpis as $k)
                        <div class="col-md">
                            <div class="card card-flush h-100">
                                <div class="card-body py-6">
                                    <div class="text-muted fs-7 mb-1">{{ $k['label'] }}</div>
                                    <div class="fw-bold fs-2 text-{{ $k['color'] }}">{{ $k['value'] }}</div>
                                    @if ($k['sub'])
                                        <div class="text-muted fs-8 mt-1">{{ $k['sub'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Daily breakdown --}}
                <div class="card card-flush">
                    <div class="card-header pt-6"><h3 class="card-title">Daily breakdown</h3></div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th>Date</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Tips</th>
                                        <th class="text-end">Cash</th>
                                        <th class="text-end">Card</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($daily as $d)
                                        <tr>
                                            <td class="fw-bold">{{ \Carbon\Carbon::parse($d['date'])->format('D, M j Y') }}</td>
                                            <td class="text-center">{{ $d['orders'] }}</td>
                                            <td class="text-end">${{ number_format($d['subtotal'], 2) }}</td>
                                            <td class="text-end text-success">${{ number_format($d['tips'], 2) }}</td>
                                            <td class="text-end">${{ number_format($d['cash'], 2) }}</td>
                                            <td class="text-end">${{ number_format($d['card'], 2) }}</td>
                                            <td class="text-end fw-bold">${{ number_format($d['total'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-10">No sales in this range.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
