@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'sliders'])
@section('title', isset($slider) ? __('admin.global.edit_slider') : __('admin.global.add_new_slider'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.sliders.index') }}"
          action="{{ isset($slider) ? route('admin.sliders.update', $slider->id) : route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($slider)
            @method('PATCH')
        @endisset

        <div class="page-content-header mb-5">
            <h2 class="table-title">{{ isset($slider) ? __('admin.global.edit_slider') : __('admin.global.add_new_slider') }}</h2>
        </div>
        
        <!-- Sidebar: Status and Logo Image Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <!-- slider Logo Section -->
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($slider) && $slider->image ? asset($slider->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content: slider Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush">
                <div class="card-body">
                    <div class="mb-5">
                        <label class="required form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter slider title"
                               value="{{ isset($slider) ? $slider->title : '' }}">
                    </div>
                    {{-- <div class="mb-5">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="Enter slider subtitle"
                               value="{{ isset($slider) ? $slider->subtitle : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ isset($slider) ? $slider->description : '' }}</textarea>
                    </div> --}}
                    <div class="mb-5">
                        <label class="form-label">Button text</label>
                        <input type="text" name="button_text" class="form-control" placeholder="Enter slider button text"
                               value="{{ isset($slider) ? $slider->button_text : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Button link</label>
                        <input type="text" name="button_link" class="form-control" placeholder="Enter slider button link"
                               value="{{ isset($slider) ? $slider->button_link : '' }}">
                    </div>
                    {{-- <div class="mb-5">
                        <label class="form-label">Order</label>
                        <input type="text" name="order" class="form-control" placeholder="Enter slider order"
                               value="{{ isset($slider) ? $slider->order : '' }}">
                    </div> --}}
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end">
                    <button type="submit" id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
