@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'transactions'])
@section('title', 'Transactions')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="page-content-header mb-5">
                    <h2 class="table-title">Transactions</h2>
                    <div class="text-muted fs-8 mt-1">
                        Every field of every <strong>settled</strong> sale taken on the POS tablet.
                        Card payments that never cleared are excluded from this list and from every total.
                    </div>
                </div>

                {{-- Filters --}}
                <div class="card card-flush mb-5">
                    <div class="card-body py-5">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="fs-8 fw-semibold mb-1">From</label>
                                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="fs-8 fw-semibold mb-1">To</label>
                                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="fs-8 fw-semibold mb-1">Employee</label>
                                <select name="employee_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="fs-8 fw-semibold mb-1">Cashier</label>
                                <select name="admin_id" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach ($cashiers as $c)
                                        <option value="{{ $c->id }}" @selected(request('admin_id') == $c->id)>{{ $c->name ?? $c->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="fs-8 fw-semibold mb-1">Method</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach (['cash' => 'Cash', 'zelle' => 'Zelle', 'card' => 'Card'] as $v => $label)
                                        <option value="{{ $v }}" @selected(request('payment_method') === $v)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-8 fw-semibold mb-1">Search</label>
                                <input type="text" name="q" value="{{ request('q') }}"
                                       class="form-control form-control-sm"
                                       placeholder="Order #, customer email, reference, payment intent">
                            </div>
                            <div class="col-md-6 d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-primary">Apply</button>
                                <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-light">Reset</a>
                                <a href="{{ route('admin.transactions.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-light">Today</a>
                                <a href="{{ route('admin.transactions.index', ['from' => now()->subDay()->toDateString(), 'to' => now()->subDay()->toDateString()]) }}" class="btn btn-sm btn-light">Yesterday</a>
                                <a href="{{ route('admin.transactions.index', ['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString()]) }}" class="btn btn-sm btn-light">Last 7 days</a>
                                <a href="{{ route('admin.transactions.index', ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->endOfMonth()->toDateString()]) }}" class="btn btn-sm btn-light">This month</a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Totals for the whole filtered set --}}
                <div class="row g-4 mb-5">
                    @php
                        $cards = [
                            ['Orders',   number_format($totals['orders']),            'dark'],
                            ['Services', '$' . number_format($totals['subtotal'], 2), 'primary'],
                            ['Fees',     '$' . number_format($totals['fees'], 2),     'info'],
                            ['Tips',     '$' . number_format($totals['tips'], 2),     'success'],
                            ['Total',    '$' . number_format($totals['total'], 2),    'warning'],
                        ];
                    @endphp
                    @foreach ($cards as [$label, $value, $color])
                        <div class="col-md">
                            <div class="card card-flush h-100">
                                <div class="card-body py-5">
                                    <div class="text-muted fs-8 mb-1">{{ $label }}</div>
                                    <div class="fw-bold fs-3 text-{{ $color }}">{{ $value }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Ledger --}}
                <div class="card card-flush">
                    <div class="card-body pt-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-7 gy-4">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-8 text-uppercase">
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Cashier</th>
                                        <th>Items</th>
                                        <th class="text-end">Services</th>
                                        <th class="text-end">Fee</th>
                                        <th class="text-end">Tip</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Method</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $o)
                                        @php
                                            $methodClass = match ($o->payment_method) {
                                                'cash'  => 'badge-light-success',
                                                'zelle' => 'badge-light-warning',
                                                default => 'badge-light-info',
                                            };
                                            $fees = (float) $o->card_fee + (float) $o->tip_remainder;
                                        @endphp
                                        <tr>
                                            <td class="fw-bold text-nowrap">{{ $o->order_number }}</td>
                                            <td class="text-nowrap text-muted">
                                                {{ $o->created_at?->timezone(config('app.timezone'))->format('Y-m-d') }}
                                                <div class="fs-8">{{ $o->created_at?->timezone(config('app.timezone'))->format('g:i A') }}</div>
                                            </td>
                                            <td class="text-nowrap">{{ $o->employee->name ?? '—' }}</td>
                                            <td class="text-nowrap text-muted">{{ $o->admin->name ?? $o->admin->email ?? '—' }}</td>
                                            <td style="min-width: 200px;">
                                                @forelse ($o->items as $item)
                                                    <div class="d-flex justify-content-between gap-3">
                                                        <span>
                                                            {{ $item->quantity > 1 ? $item->quantity . '× ' : '' }}{{ $item->name }}
                                                            @if ($item->is_custom)
                                                                <span class="badge badge-light-warning fs-9">custom</span>
                                                            @endif
                                                        </span>
                                                        <span class="text-muted">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                                    </div>
                                                @empty
                                                    <span class="text-muted">—</span>
                                                @endforelse
                                            </td>
                                            <td class="text-end text-nowrap">${{ number_format((float) $o->subtotal, 2) }}</td>
                                            <td class="text-end text-nowrap {{ $fees > 0 ? 'text-primary' : 'text-muted' }}">
                                                {{ $fees > 0 ? '+$' . number_format($fees, 2) : '—' }}
                                                @if ((float) $o->tip_remainder > 0)
                                                    <div class="fs-9 text-muted">incl. {{ number_format((float) $o->tip_remainder, 2) }} tip cents</div>
                                                @endif
                                            </td>
                                            <td class="text-end text-nowrap text-success">
                                                {{ (float) $o->tip > 0 ? '$' . number_format((float) $o->tip, 2) : '—' }}
                                            </td>
                                            <td class="text-end text-nowrap fw-bold">${{ number_format((float) $o->total, 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $methodClass }} text-uppercase">{{ $o->payment_method }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.pos_orders.show', $o->id) }}" class="btn btn-sm btn-light">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="11" class="text-center text-muted py-10">No transactions match these filters.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted fs-8">
                                Showing {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }}
                                of {{ number_format($orders->total()) }}
                            </div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
