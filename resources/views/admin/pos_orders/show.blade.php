@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'pos_orders'])
@section('title', 'Order ' . $order->order_number)
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="page-content-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="table-title">Order #{{ $order->order_number }}</h2>
                        <a href="{{ route('admin.pos_orders.index') }}" class="btn btn-light">&larr; Back</a>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="card card-flush mb-5">
                    <div class="card-body py-5">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Employee</div>
                                <div class="fw-bold">{{ $order->employee->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Cashier</div>
                                <div class="fw-bold">{{ $order->admin->name ?? $order->admin->email ?? '—' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Payment method</div>
                                <div class="fw-bold text-uppercase">{{ $order->payment_method }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-8">Date</div>
                                <div class="fw-bold">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                            @if ($order->customer_email)
                                <div class="col-md-6">
                                    <div class="text-muted fs-8">Customer email</div>
                                    <div class="fw-bold">
                                        {{ $order->customer_email }}
                                        @if ($order->receipt_sent_at)
                                            <span class="badge badge-light-success ms-2">Receipt sent {{ $order->receipt_sent_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if ($order->notes)
                                <div class="col-12">
                                    <div class="text-muted fs-8">Notes</div>
                                    <div>{{ $order->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Line items --}}
                <div class="card card-flush">
                    <div class="card-header"><h3 class="card-title">Items</h3></div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                                        <th>Service</th>
                                        <th class="text-end">Unit price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Line total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td class="fw-bold">
                                                {{ $item->name }}
                                                @if ($item->is_custom)
                                                    <span class="badge badge-light-warning ms-1">custom</span>
                                                @endif
                                            </td>
                                            <td class="text-end">${{ number_format((float) $item->price, 2) }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end fw-bold">${{ number_format($item->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-semibold">Subtotal</td>
                                        <td class="text-end fw-semibold">${{ number_format((float) $order->subtotal, 2) }}</td>
                                    </tr>
                                    @if ((float) $order->card_fee > 0)
                                        <tr>
                                            <td colspan="3" class="text-end fw-semibold text-primary">
                                                Card fee
                                                <span class="text-muted fw-normal fs-8">(surcharge, kept by the shop)</span>
                                            </td>
                                            <td class="text-end fw-semibold text-primary">
                                                +${{ number_format((float) $order->card_fee, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ((float) $order->tip > 0)
                                        <tr>
                                            <td colspan="3" class="text-end fw-semibold">
                                                Tip <span class="text-muted fw-normal fs-8">(to {{ $order->employee->name ?? 'employee' }})</span>
                                            </td>
                                            <td class="text-end fw-semibold">${{ number_format((float) $order->tip, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ((float) $order->tip_remainder > 0)
                                        <tr>
                                            <td colspan="3" class="text-end fw-semibold text-primary">
                                                Tip cents
                                                <span class="text-muted fw-normal fs-8">
                                                    (fraction of the ${{ number_format($order->customer_tip, 2) }} tip — kept by the shop)
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold text-primary">
                                                +${{ number_format((float) $order->tip_remainder, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-end fs-4 fw-bold">Total</td>
                                        <td class="text-end fs-4 fw-bold">${{ number_format((float) $order->total, 2) }}</td>
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
