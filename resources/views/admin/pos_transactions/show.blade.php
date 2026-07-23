@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'pos_transactions'])
@section('title', 'Transaction #' . $transaction->id)
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="page-content-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="table-title">Transaction #{{ $transaction->id }}</h2>
                        <a href="{{ route('admin.pos_transactions.index') }}" class="btn btn-light">&larr; Back</a>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="card card-flush mb-5">
                    <div class="card-body py-5">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Employee</div>
                                <div class="fw-bold">{{ $transaction->admin->name ?? $transaction->admin->email ?? '—' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Payment method</div>
                                <div class="fw-bold text-uppercase">{{ $transaction->payment_method }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Status</div>
                                <div class="fw-bold text-capitalize">{{ $transaction->status }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Date</div>
                                <div class="fw-bold">{{ $transaction->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Line items (read-only) --}}
                <div class="card card-flush">
                    <div class="card-header"><h3 class="card-title">Items</h3></div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th>Service</th>
                                        <th class="text-end">Original price</th>
                                        <th class="text-end">Charged price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Line total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->items as $item)
                                        @php
                                            $isCustom = $item->original_price !== null
                                                && (string) $item->original_price !== (string) $item->unit_price;
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $item->service_name }}</td>
                                            <td class="text-end">
                                                {{ $item->original_price !== null ? '$' . number_format((float) $item->original_price, 2) : '—' }}
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold">${{ number_format((float) $item->unit_price, 2) }}</span>
                                                @if ($isCustom)
                                                    <span class="badge badge-light-danger ms-1">custom</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end fw-bold">${{ number_format((float) $item->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold">Subtotal</td>
                                        <td class="text-end fw-semibold">${{ number_format((float) $transaction->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fs-4 fw-bold">Total</td>
                                        <td class="text-end fs-4 fw-bold">${{ number_format((float) $transaction->total, 2) }}</td>
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
