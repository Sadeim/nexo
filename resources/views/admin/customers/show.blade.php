@extends('admin.layouts.master', ['is_active_parent' => 'customers', 'is_active' => 'customers'])
@section('title', $customer->name)
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">

                    <div class="page-content-header">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-8">
                                <h2 class="table-title">{{ $customer->name }}</h2>
                            </div>
                            <div class="col-md-4 d-flex justify-content-end">
                                <a class="btn btn-light" href="{{ route('admin.customers.index') }}">Back to customers</a>
                            </div>
                        </div>
                    </div>

                    <div class="card card-flush">
                        <div class="card-body">
                            <div class="row g-9">
                                <div class="col-md-3">
                                    <div class="text-muted fs-8 text-uppercase">Phone</div>
                                    <div class="fs-5 fw-semibold">
                                        <a href="tel:{{ \App\Support\Phone::e164($customer->phone) }}">
                                            {{ $customer->pretty_phone }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted fs-8 text-uppercase">Email</div>
                                    <div class="fs-5 fw-semibold">{{ $customer->email ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted fs-8 text-uppercase">Bookings</div>
                                    <div class="fs-5 fw-semibold">{{ $customer->bookings_count }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted fs-8 text-uppercase">SMS consent</div>
                                    <div class="fs-5 fw-semibold">
                                        @if ($customer->sms_marketing_consent)
                                            <span class="badge badge-light-success fs-7">Given</span>
                                            <div class="text-muted fs-8 mt-1">
                                                {{ optional($customer->sms_consent_at)->format('j M Y, g:i A') }}
                                                @if ($customer->consent_source)
                                                    · {{ $customer->consent_source }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge badge-light-secondary fs-7">Not given</span>
                                            <div class="text-muted fs-8 mt-1">Not in the export.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title"><h3 class="fw-bold m-0">Their bookings</h3></div>
                        </div>
                        <div class="card-body pt-0">
                            @if ($bookings->isEmpty())
                                <div class="text-muted">
                                    No bookings linked to this customer yet — their number was taken at the
                                    booking form before an appointment was made.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-4">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Service</th>
                                                <th>Barber</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach ($bookings as $booking)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('D j M Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($booking->time)->format('g:i A') }}</td>
                                                    <td>{{ $booking->service->name ?? '—' }}</td>
                                                    <td>{{ $booking->barber->name ?? 'Any available barber' }}</td>
                                                    <td>{{ $booking->status }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $bookings->links() }}
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
