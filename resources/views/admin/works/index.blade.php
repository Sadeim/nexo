@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'works'])
@section('title')
    {{ __('admin.global.works') }}
@endsection
@section('content')
@if ($section)
    <div class="container-xxl">
        <div class="card card-flush mb-5" id="sectionCard">
            {{-- صورة القسم --}}
            <div class="card-header justify-content-center p-5">
                <div class="card-toolbar">
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                        <div class="image-input-wrapper w-200px h-200px" 
                            style="background-image: url({{ $section->image ? asset($section->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                        </div>

                        {{-- زر تغيير الصورة --}}
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" 
                            data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="image" accept=".png, .jpg, .jpeg" id="sectionImageInput" />
                        </label>

                        {{-- زر الإلغاء --}}
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" 
                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>
                </div>
            </div>

            {{-- العنوان + التبديل + زر الحفظ --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title">
                        <span id="sectionTitleText">Title: {{ $section->title }}</span>
                        <input type="text" class="form-control d-none" id="sectionTitleInput" value="{{ $section->title }}">
                    </h2>
                </div>

                <div class="d-flex align-items-center gap-4">
                    {{-- Toggle --}}
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="section_toggle"
                            {{ $section->is_active ? 'checked' : '' }}
                            data-url="{{ route('admin.sections.toggle', $section->id) }}" />
                        <label class="form-check-label" for="section_toggle">
                            {{ $section->is_active ? 'Visible' : 'Hidden' }}
                        </label>
                    </div>

                    {{-- زر الحفظ --}}
                    <button class="btn btn-primary" id="editBtn">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    <button class="btn btn-sm btn-light-success d-none" id="saveBtn"
                        data-url="{{ route('admin.sections.update', $section->id) }}">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </div>

            {{-- الوصف والنوت --}}
            <div class="card-body">
                {{-- Description --}}
                <div class="mb-4">
                    <label><strong>Description:</strong></label>
                    <p id="sectionDescriptionText" class="fs-5 text-gray-700 m-0">{{ $section->description }}</p>
                    <textarea class="form-control d-none" id="sectionDescriptionInput">{{ $section->description }}</textarea>
                </div>

                {{-- Note --}}
                <div>
                    <label><strong>Note:</strong></label>
                    <p id="sectionNoteText" class="fs-6 text-muted m-0">{{ $section->note }}</p>
                    <textarea class="form-control d-none" id="sectionNoteInput">{{ $section->note }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div id="globalLoader" class="d-none globalLoader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@endif
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
                                <h2 class="table-title">{{ __('admin.global.works') }}</h2>

                            </div>
                            <div class="col-8 col-sm-12 col-md-9 col-lg-9">
                                <div class="card-toolbar flex-row-fluid d-flex justify-content-end gap-5">
                                    <!--Add new user start-->
                                    <a class="btn btn-primary" href="{{ route('admin.works.create') }}">
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
                                                <th>Title</th>
                                                <th>Category</th>
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
        window.datatable_url = "{{ route('admin.works.datatable') }}";
    </script>
    <script src="{{ asset('admin_assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js_resources/works.js') }}"></script>
    <script src="{{ asset('admin_assets/js/dashboard/handleDataTable.js') }}"></script>
@endpush
@push('modals')
 
@endpush
