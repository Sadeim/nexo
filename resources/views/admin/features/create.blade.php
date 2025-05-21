@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'features'])
@section('title', isset($feature) ? __('admin.global.edit_feature') : __('admin.global.add_new_feature'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.features.index') }}"
          action="{{ isset($feature) ? route('admin.features.update', $feature->id) : route('admin.features.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($feature)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($feature) ? __('admin.global.edit_feature') : __('admin.global.add_new_feature') }}</h2>
        </div>
        
        <!-- Sidebar: Status and Logo Image Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <!-- feature Logo Section -->
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($feature) && $feature->image ? asset($feature->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
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
        
        <!-- Main Content: feature Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush">
                <div class="card-body">
                    <div class="mb-5">
                        <label class="required form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter feature title"
                               value="{{ isset($feature) ? $feature->title : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Link</label>
                        <input type="text" name="link" class="form-control" placeholder="Enter feature link"
                               value="{{ isset($feature) ? $feature->link : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Icon</label>
                        <input type="text" name="icon" class="form-control" placeholder="Enter feature icon"
                               value="{{ isset($feature) ? $feature->icon : '' }}">
                    </div>
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
                    <a href="{{ route('admin.features.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
