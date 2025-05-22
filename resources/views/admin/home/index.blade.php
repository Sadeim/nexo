@extends('admin.layouts.master', ['is_active_parent' => '','is_active'=> 'home'])
@section('title', __('Home'))

@section('content')
<div class="d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Container-->
    <div class="container-xxl" id="kt_content_container">
        <div class="row g-5 g-xl-8">
            <!-- Visitors Count -->
            <div class="col-xl-3">
                <div class="card card-bordered">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users fs-2x text-primary"></i>
                            <div class="ms-4">
                                <div class="fs-6 fw-semibold text-muted">Total Visitors</div>
                                <div class="fs-3 fw-bold">1</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Clients Count -->
            <div class="col-xl-3">
                <div class="card card-bordered">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-tie fs-2x text-success"></i>
                            <div class="ms-4">
                                <div class="fs-6 fw-semibold text-muted">Clients</div>
                                <div class="fs-3 fw-bold">{{ $clientsCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Services Count -->
            <div class="col-xl-3">
                <div class="card card-bordered">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cogs fs-2x text-info"></i>
                            <div class="ms-4">
                                <div class="fs-6 fw-semibold text-muted">Services</div>
                                <div class="fs-3 fw-bold">{{ $servicesCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Projects Count -->
            <div class="col-xl-3">
                <div class="card card-bordered">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-briefcase fs-2x text-warning"></i>
                            <div class="ms-4">
                                <div class="fs-6 fw-semibold text-muted">Projects</div>
                                <div class="fs-3 fw-bold">{{ $worksCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-5 mt-4">
            <!--begin::Newsletters-->
            <div class="col-xl-6">
                <div class="card card-custom gutter-b">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Newsletters</h3>
                        </div>
                        <div class="card-body">
                            <div id="newsletters_chart" style="height: 250px;"></div>
                            <div class="mt-5">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bullet bg-primary me-3"></div>
                                    <div class="flex-grow-1">
                                        <div class="fs-6 fw-semibold text-gray-700">New Subscribers This Month</div>
                                        <div class="fs-5 fw-bold">{{ $newSubscribersThisMonth }}</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bullet bg-success me-3"></div>
                                    <div class="flex-grow-1">
                                        <div class="fs-6 fw-semibold text-gray-700">Total Subscribers</div>
                                        <div class="fs-5 fw-bold">{{ $totalSubscribers }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Newsletters-->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Latest Clients</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-rounded table-striped border gy-7 gs-7">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                        <th>Name</th>
                                        <th>Link</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentClients as $client)
                                    <tr>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->link }}</td>
                                        <td>{{ $client->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge badge-light-success">Active</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-5 mt-5">
            <!--begin::Annual Growth-->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Annual Growth</h3>
                    </div>
                    <div class="card-body">
                        <div id="annual_growth_chart" style="height: 350px;"></div>
                    </div>
                </div>  
            </div>
            <!--end::Annual Growth-->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Projects Distribution by Type</h3>
                    </div>
                    <div class="card-body">
                        <div id="works_by_type_chart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('admin_assets/js/scripts.bundle.js') }}"></script>

<script src="{{ asset('admin_assets/js/echarts.min.js') }}"></script>

<script>
    var annualGrowthChart = echarts.init(document.getElementById('annual_growth_chart'));
    annualGrowthChart.setOption({
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: ['Projects', 'Clients', 'Blogs']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: 'Projects',
                type: 'line',
                data: {!! json_encode($projectsMonthly) !!},
                smooth: true,
                lineStyle: {
                    width: 3,
                    shadowColor: 'rgba(0,0,0,0.3)',
                    shadowBlur: 10,
                    shadowOffsetY: 8
                }
            },
            {
                name: 'Clients',
                type: 'line',
                data: {!! json_encode($clientsMonthly) !!},
                smooth: true
            },
            {
                name: 'Blogs',
                type: 'line',
                data: {!! json_encode($blogsMonthly) !!},
                smooth: true
            }
        ]
    });
</script>
<script>
    var worksByTypeChart = echarts.init(document.getElementById('works_by_type_chart'));
    worksByTypeChart.setOption({
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            right: 10,
            top: 'center',
            data: {!! json_encode($worksByTypeLabels) !!}
        },
        series: [{
            name: 'Projects',
            type: 'pie',
            radius: ['50%', '70%'],
            avoidLabelOverlap: false,
            label: {
                show: false,
                position: 'center'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '18',
                    fontWeight: 'bold'
                }
            },
            labelLine: {
                show: false
            },
            data: {!! json_encode($worksByTypeData) !!}
        }]
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var chart = echarts.init(document.getElementById('newsletters_chart'));

        var chartData = {
            months: {!! json_encode($newsletterChartData['months']) !!},
            subscribers: {!! json_encode($newsletterChartData['subscribers']) !!}
        };

        var options = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['New Subscribers'],
                textStyle: {
                    fontFamily: 'Tajawal, sans-serif'
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: chartData.months,
                axisLabel: {
                    fontFamily: 'Tajawal, sans-serif'
                }
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    fontFamily: 'Tajawal, sans-serif'
                }
            },
            series: [
                {
                    name: 'New Subscribers',
                    type: 'bar',
                    data: chartData.subscribers,
                    itemStyle: {
                        color: '#3699FF'
                    },
                    label: {
                        show: true,
                        position: 'top',
                        fontFamily: 'Tajawal, sans-serif'
                    }
                }
            ]
        };

        chart.setOption(options);

        window.addEventListener('resize', function() {
            chart.resize();
        });
    });
</script>
@endpush
