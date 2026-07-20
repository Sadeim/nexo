@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'pos_transactions'])
@section('title', 'POS Transactions')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl accountTable">
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="page-content-header">
                        <div class="row justify-content-between">
                            <div class="col-12">
                                <h2 class="table-title">POS Transactions</h2>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card card-flush mb-5">
                        <div class="card-body py-5">
                            <div class="row g-4 align-items-end">
                                <div class="col-md-4">
                                    <label class="fs-7 fw-semibold mb-2">Employee</label>
                                    <select id="f_admin" class="form-select form-select-sm">
                                        <option value="">All employees</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name ?? $emp->email }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-7 fw-semibold mb-2">From date</label>
                                    <input type="date" id="f_from" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-7 fw-semibold mb-2">To date</label>
                                    <input type="date" id="f_to" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button class="btn btn-sm btn-primary" onclick="applyPosFilter()">Apply</button>
                                    <button class="btn btn-sm btn-light" onclick="resetPosFilter()">Reset</button>
                                </div>
                            </div>
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
                                                <th>Invoice</th>
                                                <th>Employee</th>
                                                <th>Total</th>
                                                <th>Method</th>
                                                <th>Date</th>
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
        window.datatable_url = "{{ route('admin.pos_transactions.datatable') }}";

        function applyPosFilter() {
            myData = {
                admin_id: document.getElementById('f_admin').value,
                date_from: document.getElementById('f_from').value,
                date_to: document.getElementById('f_to').value
            };
            $('#oc_datatable').DataTable().ajax.reload();
        }

        function resetPosFilter() {
            document.getElementById('f_admin').value = '';
            document.getElementById('f_from').value = '';
            document.getElementById('f_to').value = '';
            myData = {};
            $('#oc_datatable').DataTable().ajax.reload();
        }
    </script>
    <script src="{{ asset('admin_assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js_resources/pos_transactions.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dashboard/handleDataTable.js') }}"></script>
@endpush
