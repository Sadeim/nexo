@extends('admin.layouts.master', ['is_active_parent' => 'attribute_values','is_active'=> 'attribute_values'])
@section('title')
    {{ __('admin.global.add_new_attribute_value') }}
@endsection
@section('content')
    <form id="kt_form" class="form row" data-kt-redirect="{{ route('admin.attribute_values.index') }}"
            action="{{ isset($attribute_value) ? route('admin.attribute_values.update' ,  $attribute_value->id) : route('admin.attribute_values.store') }}">
        @csrf
        @isset($attribute_value)
            @method('PATCH')
        @endif

        <div class="page-content-header">
            <h2 class="table-title">{{ __('admin.global.add_new_attribute_value') }}</h2>
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
                                name="status" @if(isset($attribute_value) && $attribute_value->status == 1) checked="checked" @endif value="1" >
                            <span class="form-check-label fw-bold text-muted"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card card-flush ">
                <div class="salesTitle">
                    <h3>{{ __('admin.global.attribute') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <select class="form-select mb-2" id="i_select2_int" name="attribute_id"
                        data-placeholder="{{ isset($attribute_value) ? $attribute_value->attribute->name: __('admin.global.choose_attribute') }}" data-allow-clear="true">
                        <option></option>
                        @foreach ($attributes as $item)
                            <option value="{{ $item->id }}"
                                @if(isset($attribute_value) && $item->id == $attribute_value->attribute_id) selected @endif>{{ $item->name }}
                            </option>
                        @endforeach
                    </select>
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
                            <div class="tab-pane fade arabic-tab active show" id="name_and_description" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 fv-row fv-plugins-icon-container ">
                                        <div class="mb-5 fv-row">
                                            <label class="required form-label">
                                                {{ __('admin.global.attribute_value_name') }}
                                            </label>
                                            <input type="text" name="name" id="name" class="form-control mb-2"
                                                placeholder="{{ __('admin.global.attribute_value_name') }}"
                                                value="{{ isset($attribute_value) ? optional($attribute_value)->name ?? '' : '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('admin.global.attribute_value_description') }}</label>
                                    <textarea name="description" id="summernote1">{{ isset($attribute_value) ? optional($attribute_value)->description ?? '' : '' }}</textarea>
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
                            <a href="{{ route('admin.attribute_values.index') }}" id="kt_ecommerce_add_product_cancel"
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

    {{-- <script src="{{ asset('admin_assets/js/image-input.js') }}"></script> --}}

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
    </script>
    <script>
        $(function () {
            $("#lc_select2_int").select2();
            $("#i_select2_int").select2();
        });
    </script>
@endpush
