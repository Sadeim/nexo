@extends('admin.layouts.master', ['is_active_parent' => 'reports', 'is_active' => 'services_report'])
@section('title', 'Services performance')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="page-content-header mb-5 d-flex justify-content-between align-items-center">
                    <h2 class="table-title">Services performance</h2>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light">&larr; Reports</a>
                </div>

                @include('admin.reports._filter', ['route' => 'admin.reports.services'])

                <div class="card card-flush">
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th>Service</th>
                                        <th class="text-center">Times sold</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">Avg. price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rows as $r)
                                        <tr>
                                            <td class="fw-bold">
                                                {{ $r->name }}
                                                @if ($r->is_custom)
                                                    <span class="badge badge-light-warning ms-1">custom</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ (int) $r->qty }}</td>
                                            <td class="text-center">{{ (int) $r->line_count }}</td>
                                            <td class="text-end fw-bold">${{ number_format((float) $r->revenue, 2) }}</td>
                                            <td class="text-end">
                                                ${{ $r->qty > 0 ? number_format((float) $r->revenue / (int) $r->qty, 2) : '0.00' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-10">No sales in this range.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold border-top">
                                        <td>TOTAL</td>
                                        <td class="text-center">{{ $totals['items'] }}</td>
                                        <td class="text-center">{{ $totals['lines'] }}</td>
                                        <td class="text-end">${{ number_format($totals['revenue'], 2) }}</td>
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
@endsection
