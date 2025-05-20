@extends('admin.layouts.master', ['is_active_parent' => 'about', 'is_active' => 'how'])
@section('title', isset($how) ? 'Edit Work' : __('admin.global.add_new_work'))
@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.how.index') }}"
          action="{{ isset($how) ? route('admin.how.update', $how->id) : route('admin.how.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($how)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($how) ? 'Edit How We Work' : __('admin.global.add_new_how') }}</h2>
        </div>

        <!-- Sidebar: Status Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Is featured</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input btn active_operation" type="checkbox" name="is_featured"
                                   @if(isset($how) && $how->is_featured == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div>
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($how) && $how->image ? asset($how->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
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

        <!-- Main Content: Service Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">

                    <!-- Title & Description -->
                    <div class="mb-5">
                        <label class="required form-label">{{ __('admin.form.title') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Service Title"
                               value="{{ isset($how) ? $how->name : ''}}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">{{ __('admin.form.description') }}</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Enter Service Description">{{ isset($how) ? $how->description : '' }}</textarea>
                    </div>

                    <!-- Tap 1 -->
                    <div class="row">
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap1_name') }}</label>
                            <input type="text" name="tap1_name" class="form-control" placeholder="Enter Name"
                                   value="{{ isset($how) ? $how->tap1_name : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap1_number') }}</label>
                            <input type="text" name="tap1_number" class="form-control" placeholder="Enter Number"
                                   value="{{ isset($how) ? $how->tap1_number : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap1_content') }}</label>
                            <input type="text" name="tap1_content" class="form-control" placeholder="Enter Content"
                                   value="{{ isset($how) ? $how->tap1_content : ''}}">
                        </div>
                    </div>

                    <!-- Tap 2 -->
                    <div class="row">
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap2_name') }}</label>
                            <input type="text" name="tap2_name" class="form-control" placeholder="Enter Name"
                                   value="{{ isset($how) ? $how->tap2_name : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap2_number') }}</label>
                            <input type="text" name="tap2_number" class="form-control" placeholder="Enter Number"
                                   value="{{ isset($how) ? $how->tap2_number : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap2_content') }}</label>
                            <input type="text" name="tap2_content" class="form-control" placeholder="Enter Content"
                                   value="{{ isset($how) ? $how->tap2_content : ''}}">
                        </div>
                    </div>

                    <!-- Tap 3 -->
                    <div class="row">
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap3_name') }}</label>
                            <input type="text" name="tap3_name" class="form-control" placeholder="Enter Name"
                                   value="{{ isset($how) ? $how->tap3_name : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap3_number') }}</label>
                            <input type="text" name="tap3_number" class="form-control" placeholder="Enter Number"
                                   value="{{ isset($how) ? $how->tap3_number : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap3_content') }}</label>
                            <input type="text" name="tap3_content" class="form-control" placeholder="Enter Content"
                                   value="{{ isset($how) ? $how->tap3_content : ''}}">
                        </div>
                    </div>

                    <!-- Tap 4 -->
                    <div class="row">
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap4_name') }}</label>
                            <input type="text" name="tap4_name" class="form-control" placeholder="Enter Name"
                                   value="{{ isset($how) ? $how->tap4_name : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap4_number') }}</label>
                            <input type="text" name="tap4_number" class="form-control" placeholder="Enter Number"
                                   value="{{ isset($how) ? $how->tap4_number : ''}}">
                        </div>
                        <div class="col-md-4 mb-5">
                            <label class="required form-label">{{ __('admin.form.tap4_content') }}</label>
                            <input type="text" name="tap4_content" class="form-control" placeholder="Enter Content"
                                   value="{{ isset($how) ? $how->tap4_content : ''}}">
                        </div>
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
                    <a href="{{ route('admin.how.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
