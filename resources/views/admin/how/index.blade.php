@extends('admin.layouts.master', ['is_active_parent' => 'about','is_active'=> 'how'])
@section('title')
    {{ __('admin.global.how') }}
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
                                <h2 class="table-title">{{ __('admin.global.how') }}</h2>

                            </div>
                            <div class="col-8 col-sm-12 col-md-9 col-lg-9">
                                <div class="card-toolbar flex-row-fluid d-flex justify-content-end gap-5">
                                    <!--Add new user start-->
                                    <a class="btn btn-primary" href="{{ route('admin.how.create') }}">
                                        {{ __('admin.global.add_new_work') }}
                                        <span class="svg-icon svg-icon-2">
                                            +
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!----------------------------------------Tabs Start---------------------------->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                           
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
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 text-center">
                                                <th>#</th>
                                                <th>{{ __('admin.form.name') }}</th>
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
        window.datatable_url = "{{ route('admin.how.datatable') }}";
    </script>
    <script src="{{ asset('admin_assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js_resources/how.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dashboard/handleDataTable.js') }}"></script>
@endpush
@push('modals')

@endpush
