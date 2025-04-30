@extends('admin.layouts.master', ['is_active_parent' => 'banners','is_active'=> 'banners'])
@section('title', isset($banner) ? __('admin.global.edit_banner') : __('admin.global.add_new_banner'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.banners.index') }}"
            action="{{ isset($banner) ? route('admin.banners.update' ,  $banner->id) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($banner)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ __('admin.global.add_new_banner') }}</h2>
        </div>
        
        <!-- Sidebar: Status Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            {{-- <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>{{ __('admin.form.status') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input btn active_operation" type="checkbox" name="status"
                                @if(isset($banner) && $banner->status == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div> --}}
            <!-- صورة Banner -->
            <div class="card card-flush">
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url(@if(isset($banner) && $banner->image) {{ asset($banner->image) }} @else {{ asset('admin_assets/media/svg/files/blank-image.svg') }} @endif)"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: Banner Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="salesTitle">
                    <h3>{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <!-- Title Field -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Banner title</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter Banner Title"
                                    value="{{ isset($banner) ? $banner->title : '' }}">
                            </div>
                        </div>
                        <!-- Subtitle Field -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Banner subtitle</label>
                                <input type="text" name="sub_title" class="form-control" placeholder="Enter Banner Subtitle"
                                    value="{{ isset($banner) ? $banner->sub_title : '' }}">
                            </div>
                        </div>
                    </div>
                    <!-- Button Text and Link Fields -->
                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Button text</label>
                                <input type="text" name="button_text" class="form-control" placeholder="Enter Button Text"
                                    value="{{ isset($banner) ? $banner->button_text : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Button link</label>
                                <input type="text" name="button_link" class="form-control" placeholder="Enter Button Link"
                                    value="{{ isset($banner) ? $banner->button_link : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description">{{ isset($banner) ? $banner->description : '' }}</textarea>

                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end right">
                    <button type="submit" id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.banners.index') }}" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5 cancel">
                        {{ __('admin.form.cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
