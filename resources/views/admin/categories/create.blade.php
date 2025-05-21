@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'categories'])
@section('title')
    {{ __('admin.global.add_new_category') }}
@endsection
@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.categories.index') }}"
            action="{{ isset($category) ? route('admin.categories.update' ,  $category->id) : route('admin.categories.store') }}">
        @csrf
        @isset($category)
            @method('PATCH')
        @endif

        <div class="page-content-header mb-5">
            <h2 class="table-title">{{ __('admin.global.add_new_category') }}</h2>
        </div>
        
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7 ">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title ">
                        <h3>{{__('admin.form.status')}}</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input btn active_operation" style="margin: 0 auto;" type="checkbox"
                                name="status" @if(isset($category) && $category->status == 1) checked="checked" @endif value="1" >
                            <span class="form-check-label fw-bold text-muted"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card card-flush">
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline image-input-placeholder image-input-empty" data-kt-image-input="true">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url(@if(isset($category) && $category->image !== null) {{ asset($category->image) }} @else {{ asset('admin_assets/media/svg/files/blank-image.svg') }} @endif)"></div>
                            <!--end::Preview existing avatar-->
                            <!--begin::Edit-->
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <!--begin::Inputs-->
                                <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                                {{-- <input type="hidden" name="avatar_remove" /> --}}
                                <!--end::Inputs-->
                            </label>
                            <!--end::Edit-->
                            <!--begin::Cancel-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Cancel-->
                            <!--begin::Remove-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Remove-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="d-flex flex-column gap-5">
                <div class="card card-flush generalDataTap">
                    <div class="salesTitle">
                        <h3>{{__('admin.global.name_and_description')}}</h3>
                    </div>
                    <div class="card-body pt-0">
                       
                        <div class="tab-content mt-5" id="myTabContent">
                          
                            <div class="tab-pane fade arabic-tab active show">
                                <div class="row">
                                    <div class="col-md-12 fv-row fv-plugins-icon-container ">
                                        <div class="mb-5 fv-row">
                                            <label class="required form-label">
                                                {{ __('admin.global.category_name') }}
                                            </label>
                                            <input type="text" name="name" id="name" class="form-control mb-2"
                                                placeholder="{{ __('admin.global.category_name') }}"
                                                value="{{ isset($category) ? $category->name : '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('admin.global.category_description') }}</label>
                                    <textarea name="description" id="summernote">{{ isset($category) ? $category->description : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end right">
                    <button type="submit" id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.categories.index') }}" id="kt_ecommerce_add_product_cancel"
                        class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
    <script src="{{ asset('admin_assets/js/summernote-lite.min.js') }}"></script>

    <script>
        $('#summernote').summernote({
            placeholder: '{{__('admin.global.type_your_text_here')}}',
            tabsize: 2,
            height: 120,
            lang: 'ar-AR',
            toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>
@endpush
