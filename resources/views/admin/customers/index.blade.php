@extends('admin.layouts.master', ['is_active_parent' => 'customers', 'is_active' => 'customers'])
@section('title', 'Customers')
@section('content')
    <style>
        /* A phone number broken across three lines is not a phone number. */
        #oc_datatable td:nth-child(2),
        #oc_datatable td:nth-child(3),
        #oc_datatable td:nth-child(6) { white-space: nowrap; }
    </style>

    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl accountTable">
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="page-content-header">
                        <div class="row justify-content-between">
                            <div class="col-3 col-sm-12 col-md-3 col-lg-3">
                                <h2 class="table-title">Customers</h2>
                            </div>
                            <div class="col-8 col-sm-12 col-md-9 col-lg-9">
                                <div class="card-toolbar flex-row-fluid d-flex justify-content-end gap-5">
                                    {{-- The whole point of the list. Says what it
                                         contains on the button, because a file of
                                         phone numbers should never be ambiguous
                                         about who agreed to be in it. --}}
                                    <a class="btn btn-primary" href="{{ route('admin.customers.export') }}">
                                        Export {{ $consented }} consented {{ Str::plural('number', $consented) }} (CSV)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-5">
                        <div class="fw-semibold fs-7 text-gray-700">
                            <strong>{{ $total }}</strong> {{ Str::plural('customer', $total) }} in total,
                            <strong>{{ $consented }}</strong> of whom agreed to be texted.
                            Only those {{ $consented }} are in the export — consent is given at the booking
                            form and cannot be granted from here.
                        </div>
                    </div>

                    <div class="card card-flush">
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5"></div>
                        <div class="card-body pt-0">
                            <div id="kt_ecommerce_sales_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer"
                                        id="oc_datatable">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 text-center">
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Bookings</th>
                                                <th>Last booked</th>
                                                <th>SMS</th>
                                                <th>Added</th>
                                                <th>{{ __('admin.form.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        window.datatable_url = "{{ route('admin.customers.datatable') }}";

        window.columns = [
            { data: 'id' },
            { data: 'name' },
            { data: 'phone' },
            { data: 'email' },
            { data: 'bookings_count' },
            { data: 'last_booked_at' },
            { data: 'sms_marketing_consent' },
            { data: 'created_at' },
            { data: 'operations' }
        ];
        window.columnDefs = [
            { targets: 0, orderable: false, sorting: false },
            { targets: -1, orderable: false },
        ];
    </script>
    <script src="{{ asset('admin_assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dashboard/handleDataTable.js') }}"></script>
@endpush
