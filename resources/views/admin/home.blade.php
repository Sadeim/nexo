@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'home'])
@section('content')
    <div class="d-flex flex-stack mb-5">
        <div class="d-flex align-items-center position-relative my-1">
            <span class="svg-icon svg-icon-2">...</span>
            <input type="text" data-kt-docs-table-filter="search" class="form-control form-control-solid w-250px ps-15"
                placeholder="Search Customers" />
        </div>
        <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
            <button type="button" class="btn btn-primary me-3" data-bs-toggle="tooltip" title="Coming Soon">
                <span class="svg-icon svg-icon-2">...</span>
                Filter
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" title="Coming Soon">
                <span class="svg-icon svg-icon-2">...</span>
                Add Customer
            </button>
        </div>
        <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
            <div class="fw-bold me-5">
                <span class="me-2" data-kt-docs-table-select="selected_count"></span> Selected
            </div>

            <button type="button" class="btn btn-danger" data-bs-toggle="tooltip"
                data-kt-docs-table-select="delete_selected">
                Selection Action
            </button>
        </div>
    </div>
    <table id="oc_datatable" class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                <th class="w-10px pe-2">
                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                            data-kt-check-target="#oc_datatable .form-check-input" value="1" />
                    </div>
                </th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Payment Method</th>
                <th>Created Date</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        </tbody>
    </table>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dashboard/handleDataTable.js') }}"></script>
@endpush
