{{--
    Shared date-range filter for the reports. Requires:
      $from, $to  : Carbon dates
      $route      : route name of the CURRENT report (e.g. admin.reports.sales)
--}}
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
                <a href="{{ route($route, ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-light">Today</a>
                <a href="{{ route($route, ['from' => now()->subDay()->toDateString(), 'to' => now()->subDay()->toDateString()]) }}" class="btn btn-sm btn-light">Yesterday</a>
                <a href="{{ route($route, ['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-light">Last 7 days</a>
                <a href="{{ route($route, ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->endOfMonth()->toDateString()]) }}" class="btn btn-sm btn-light">This month</a>
                <a href="{{ route($route, ['from' => now()->subDays(29)->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-light">Last 30 days</a>
            </div>
        </form>
        <div class="text-muted fs-8 mt-3">
            Range: <strong>{{ $from->format('D, M j Y') }}</strong> → <strong>{{ $to->format('D, M j Y') }}</strong>
            &middot; Only <em>completed</em> Flutter POS orders count.
        </div>
    </div>
</div>
