@extends('admin.layouts.master', ['is_active_parent' => 'about', 'is_active' => 'reasons'])
@section('title', isset($reason) ? __('admin.global.edit_reason') : __('admin.global.add_new_reason'))
@section('content')

    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm"
          data-kt-redirect="{{ route('admin.reasons.index') }}"
          action="{{ isset($reason) ? route('admin.reasons.update', $reason->id) : route('admin.reasons.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @isset($reason)
            @method('PATCH')
        @endisset

        <!-- Page Header -->
        <div class="page-content-header mb-5">
            <h2 class="table-title">
                {{ isset($reason) ? __('admin.global.edit_reason') : __('admin.global.add_new_reason') }}
            </h2>
        </div>

        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    {{-- <label class="available">Icon Image</label> --}}
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($service) && $service->icon ? asset($service->icon) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="icon" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- Main Content: Reason Fields -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">Reason Details</h3>
                </div>
                <div class="card-body pt-0">
                    <!-- Reason 1 -->
                    <div class="row align-items-end mb-8 pb-6">
                        <div class="col-md-12 fv-row">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control"
                                   value="{{ old('title', isset($reason) ? $reason->title : '') }}"
                                   placeholder="Enter title">
                        </div>
                    </div>
                    <div class="row align-items-end mb-8 pb-6">
                        <div class="col-md-12 fv-row">
                            <label class="form-label">Description</label>
                            <input type="text" name="text" class="form-control"
                                    value="{{ old('text', isset($reason) ? $reason->text: '') }}"
                                    placeholder="Enter description">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Submit Buttons -->
        <div class="page-buttuns mt-5">
            <div class="d-flex justify-content-end">
                <button type="submit" id="kt_submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                    <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
                <a href="{{ route('admin.reasons.index') }}" class="btn btn-light me-5 cancel">
                    {{ __('admin.form.cancel') }}
                </a>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush