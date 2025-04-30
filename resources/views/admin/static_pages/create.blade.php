@extends('admin.layouts.master', ['is_active_parent' => 'static_pages','is_active'=> 'static_pages'])
@section('title')
    {{ __('admin.global.add_new_static_page') }}
@endsection
@section('content')
    <form id="kt_form" class="form row" data-kt-redirect="{{ route('admin.static_pages.index') }}"
            action="{{ isset($static_page) ? route('admin.static_pages.update' ,  $static_page->id) : route('admin.static_pages.store') }}">
        @csrf
        @isset($static_page)
            @method('PATCH')
        @endif

        <div class="page-content-header">
            <h2 class="table-title">{{ __('admin.global.add_new_static_page') }}</h2>
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
                                name="status" @if(isset($static_page) && $static_page->status == 1) checked="checked" @endif value="1" >
                            <span class="form-check-label fw-bold text-muted"></span>
                        </label>
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
                            <div class="tab-pane fade generalTap-pane active show">
                                <div class="row">
                                    <div class="col-md-12 fv-row fv-plugins-icon-container ">
                                        <div class="mb-5 fv-row">
                                            <label class="required form-label">{{ __('admin.global.static_page_title') }}</label>
                                            <input type="text" name="title_ar" class="form-control mb-2 w-100"
                                                placeholder="{{ __('admin.global.static_page_title') }}"
                                                value="{{ isset($static_page) ? optional($static_page)->translate('ar')->title ?? '' : '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('admin.global.static_page_content') }} </label>
                                    <textarea name="content_ar" id="summernote1">{{ isset($static_page) ? optional($static_page)->translate('ar')->content ?? '' : '' }}</textarea>
                                </div>
                            </div>
                            <div class="tab-pane fade arabic-tab {{($lang == 'ar') ? '' : 'active show en'}}" id="name_and_description_en" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 fv-row fv-plugins-icon-container ">
                                        <div class="mb-5 fv-row">
                                            <label class="required form-label">
                                                {{ __('admin.global.static_page_name') }}
                                            </label>
                                            <input type="text" name="title_en" id="title_en" class="form-control mb-2"
                                                placeholder="{{ __('admin.global.static_page_name') }}"
                                                value="{{ isset($static_page) ? optional($static_page)->title ?? '' : '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('admin.global.static_page_content') }}</label>
                                    <textarea name="content_en" id="summernote1_en">{{ isset($static_page) ? optional($static_page)->content ?? '' : '' }}</textarea>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="card card-flush generalDataTap">
                    <div class="card-body pt-0">
                        <div class="mt-5">
                            <div class="row">
                                <div class="col-md-12 fv-row fv-plugins-icon-container ">
                                    <div class="mb-5 fv-row">
                                        <label class="required form-label">Slug</label>
                                        <input type="text" name="slug" class="form-control mb-2 w-100"
                                            placeholder="Slug"
                                            value="{{ isset($static_page) ? $static_page->slug:'' }}" />
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
                            <a href="{{ route('admin.static_pages.index') }}" id="kt_ecommerce_add_product_cancel"
                                class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </form>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
    <script src="{{ asset('admin_assets/js/summernote-lite.min.js') }}"></script>

    <script>
        $('#summernote1').summernote({
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
        $('#summernote1_en').summernote({
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
    <script>
        $(function () {
            $("#discount_type").select2();
            $("#products").select2();
            $("#categories").select2();
        });
    </script>
@endpush
