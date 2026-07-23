@extends('admin.layouts.master', ['is_active_parent' => 'reports', 'is_active' => 'reports_hub'])
@section('title', 'Reports')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="page-content-header mb-5">
                    <h2 class="table-title">Reports</h2>
                </div>

                <div class="row g-5">
                    @php
                        $cards = [
                            [
                                'title' => 'Sales overview',
                                'desc'  => 'Total revenue by day, split by cash / card. Average ticket.',
                                'icon'  => 'fa-chart-line',
                                'href'  => route('admin.reports.sales'),
                                'color' => 'primary',
                            ],
                            [
                                'title' => 'Services',
                                'desc'  => 'Which services sell the most (count + revenue).',
                                'icon'  => 'fa-scissors',
                                'href'  => route('admin.reports.services'),
                                'color' => 'info',
                            ],
                            [
                                'title' => 'Cashiers',
                                'desc'  => 'Orders and revenue per cashier / admin.',
                                'icon'  => 'fa-user-shield',
                                'href'  => route('admin.reports.cashiers'),
                                'color' => 'warning',
                            ],
                            [
                                'title' => 'Employee Payroll',
                                'desc'  => 'What you owe each employee (commission + tips) and payments.',
                                'icon'  => 'fa-money-check-dollar',
                                'href'  => route('admin.reports.employees.index'),
                                'color' => 'success',
                            ],
                        ];
                    @endphp

                    @foreach ($cards as $c)
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ $c['href'] }}" class="card card-flush h-100 text-decoration-none">
                                <div class="card-body py-8">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="p-3 rounded bg-light-{{ $c['color'] }}">
                                            <i class="fa-solid {{ $c['icon'] }} fs-2 text-{{ $c['color'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold fs-4 text-gray-900 mb-1">{{ $c['title'] }}</div>
                                            <div class="text-muted fs-7">{{ $c['desc'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
