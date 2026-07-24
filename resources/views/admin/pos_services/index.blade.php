@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'pos_services'])
@section('title', 'POS Services')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl accountTable">
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">

                    <div class="page-content-header">
                        <div class="row justify-content-between">
                            <div class="col-3 col-sm-12 col-md-3 col-lg-3">
                                <h2 class="table-title">POS Services</h2>
                            </div>
                            <div class="col-8 col-sm-12 col-md-9 col-lg-9">
                                <div class="card-toolbar flex-row-fluid d-flex justify-content-end gap-3">
                                    <form method="POST" action="{{ route('admin.pos_services.import') }}"
                                          onsubmit="return confirm('Copy all website services (that have a price) into POS Services? Only new names will be added — existing ones stay untouched.');">
                                        @csrf
                                        <button class="btn btn-light-primary" type="submit">
                                            <i class="fa fa-cloud-arrow-down me-1"></i> Import from website
                                        </button>
                                    </form>
                                    <a class="btn btn-primary" href="{{ route('admin.pos_services.create') }}">
                                        Add new service <span class="svg-icon svg-icon-2">+</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

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
                                                <th>{{ __('admin.form.name') }}</th>
                                                <th>Price</th>
                                                <th>Sort</th>
                                                <th>Status</th>
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
        window.datatable_url = "{{ route('admin.pos_services.datatable') }}";

        window.columns = [
            { data: 'id' },
            { data: 'name' },
            { data: 'price' },
            { data: 'sort_order' },
            { data: 'status' },
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
