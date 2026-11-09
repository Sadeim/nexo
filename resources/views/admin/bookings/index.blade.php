@extends('admin.layouts.master', ['is_active_parent' => 'home', 'is_active' => 'bookings'])
@section('title')
    {{ __('admin.global.bookings') }}
@endsection
@section('content')
    <div class="d-flex flex-column flex-column-fluid customerView" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid chartAccount customView" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl accountTable">
                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="page-content-header">
                        <div class="row justify-content-between">
                            <div class="col-3 col-sm-12 col-md-3 col-lg-3">
                                <h2 class="table-title">{{ __('admin.global.bookings') }}</h2>

                            </div>
                            <div class="col-8 col-sm-12 col-md-9 col-lg-9">
                                <div class="card-toolbar flex-row-fluid d-flex justify-content-end gap-5">
                                    {{-- <!--Add new user start-->
                                    <a class="btn btn-primary" href="{{ route('admin.bookings.create') }}">
                                        {{ __('admin.global.add_new_booking') }}
                                        <span class="svg-icon svg-icon-2">
                                            +
                                        </span>
                                    </a> --}}
                                    <div class="card-toolbar flex-row-fluid d-flex justify-content-end gap-5">
                                        <a class="btn btn-primary" href="{{ route('admin.bookings.calendar') }}">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Calendar View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!----------------------------------------Tabs Start---------------------------->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <!--begin::Card title-->
                            {{-- <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                                rx="1" transform="rotate(45 17.0365 15.1223)"
                                                fill="currentColor"></rect>
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </div>
                                <!--end::Search-->
                                <!--begin::Export buttons-->
                                <div id="kt_ecommerce_report_views_export" class="d-none"></div>
                                <!--end::Export buttons-->
                            </div> --}}
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <div id="kt_ecommerce_sales_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer"
                                        id="oc_datatable">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr
                                                class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 text-center">
                                                <th>#</th>
                                                <th>{{ __('admin.form.name') }}</th>
                                                <th>Email</th>
                                                <th>Service</th>
                                                <th>Barber</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>{{ __('admin.form.created_at') }}</th>
                                                <th>{{ __('admin.form.actions') }}</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">

                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                </div>
                            </div>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!-----------------------------------------Tabs End----------------------------->
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        window.datatable_url = "{{ route('admin.bookings.datatable') }}";

        // Defined inline rather than in admin_assets/js_resources/bookings.js:
        // those files have 404'd on the server before, and a missing column
        // list silently empties the table.
        window.columns = [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'service' },
            { data: 'barber' },
            { data: 'date' },
            { data: 'time' },
            { data: 'created_at' },
            { data: 'operations' }
        ];
        window.columnDefs = [
            { targets: 0, orderable: false, sorting: false },
            { targets: 4, orderable: false, sorting: false },
            { targets: -1, orderable: false },
        ];
    </script>
    <script src="{{ asset('admin_assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dashboard/handleDataTable.js') }}"></script>
    <script>
        // Inline barber assignment. Delegated, because DataTables replaces the
        // rows on every draw and bound handlers would not survive a redraw.
        document.addEventListener('change', function (event) {
            const select = event.target.closest('.js-assign-barber');
            if (!select) return;

            const previous = select.dataset.current || '';
            const chosen   = select.value;

            select.disabled = true;

            fetch(select.dataset.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    // This layout publishes the token as <meta name="_token"
                    // id="csrf_token">, which is what the working admin
                    // scripts read. meta[name="csrf-token"] matches nothing.
                    'X-CSRF-TOKEN': document.getElementById('csrf_token')?.content || ''
                },
                body: JSON.stringify({ barber_id: chosen === '' ? null : chosen })
            })
                .then(response => response.json().then(body => ({ ok: response.ok, body })))
                .then(({ ok, body }) => {
                    if (!ok) {
                        // Put the dropdown back: the booking did not change.
                        select.value = previous;
                        alert(body.message || 'Could not assign that barber.');
                        return;
                    }
                    select.dataset.current = chosen;
                })
                .catch(() => {
                    select.value = previous;
                    alert('Could not reach the server.');
                })
                .finally(() => {
                    select.disabled = false;
                });
        });
    </script>
@endpush
@push('modals')
@endpush
