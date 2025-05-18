@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'aporch'])
@section('title', isset($aporch) ? __('admin.global.edit_aporch') : __('admin.global.add_new_aporch'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.aporch.index') }}"
          action="{{ isset($aporch) ? route('admin.aporch.update', $aporch->id) : route('admin.aporch.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($aporch)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($aporch) ? __('admin.global.edit_aporch') : __('admin.global.add_new_aporch') }}</h2>
        </div>

        <!-- Main Row: Left Sidebar + Right Content -->
        <div class="row g-5">

            <!-- ⬅️ القسم الأيسر: صورتين تحت بعض -->
            <div class="col-lg-3 order-1 order-lg-0 d-flex flex-column gap-5">

                <!-- الصورة الأولى -->
                <div class="card card-flush">
                    <div class="card-header justify-content-center p-5">
                        <div class="card-toolbar">
                            <div class="image-input image-input-outline" data-kt-image-input="true">
                                <div class="image-input-wrapper w-200px h-200px"
                                     style="background-image: url({{ isset($aporch) && $aporch->image1 ? asset($aporch->image1) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                                </div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                       data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" name="image1" accept=".png, .jpg, .jpeg" />
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                      data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الصورة الثانية -->
                <div class="card card-flush">
                    <div class="card-header justify-content-center p-5">
                        <div class="card-toolbar">
                            <div class="image-input image-input-outline" data-kt-image-input="true">
                                <div class="image-input-wrapper w-200px h-200px"
                                     style="background-image: url({{ isset($aporch) && $aporch->image2  ? asset($aporch->image2) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                                </div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                       data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" name="image2" accept=".png, .jpg, .jpeg" />
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                      data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ➡️ القسم الأيمن: الحقول الرئيسية -->
            <div class="col-lg-9 order-0 order-lg-1">

                <div class="card card-flush generalDataTap">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                    </div>
                    <div class="card-body pt-0">

                        <!-- Title -->
                        <div class="mb-5">
                            <label class="required form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter the Title"
                                   value="{{ old('title', isset($aporch) ? $aporch->title : '') }}">
                        </div>

                        <!-- Tab 1 -->
                        <div class="mb-5">
                            <label class="required form-label">Tab 1 Name</label>
                            <input type="text" name="tap1_name" class="form-control" placeholder="Enter Tab Name"
                                   value="{{ old('tap1_name', isset($aporch) ? $aporch->tap1_name : '') }}">
                        </div>
                        <div class="mb-5">
                            <label class="required form-label">Tab 1 Content</label>
                            <input type="text" name="tap1_content" class="form-control" placeholder="Enter Tab Content"
                                   value="{{ old('tap1_content', isset($aporch) ? $aporch->tap1_content : '') }}">
                        </div>

                        <!-- Tab 2 -->
                        <div class="mb-5">
                            <label class="required form-label">Tab 2 Name</label>
                            <input type="text" name="tap2_name" class="form-control" placeholder="Enter Tab Name"
                                   value="{{ old('tap2_name', isset($aporch) ? $aporch->tap2_name : '') }}">
                        </div>
                        <div class="mb-5">
                            <label class="required form-label">Tab 2 Content</label>
                            <input type="text" name="tap2_content" class="form-control" placeholder="Enter Tab Content"
                                   value="{{ old('tap2_content', isset($aporch) ? $aporch->tap2_content : '') }}">
                        </div>

                        <!-- Tab 3 -->
                        <div class="mb-5">
                            <label class="required form-label">Tab 3 Name</label>
                            <input type="text" name="tap3_name" class="form-control" placeholder="Enter Tab Name"
                                   value="{{ old('tap3_name', isset($aporch) ? $aporch->tap3_name : '') }}">
                        </div>
                        <div class="mb-5">
                            <label class="required form-label">Tab 3 Content</label>
                            <input type="text" name="tap3_content" class="form-control" placeholder="Enter Tab Content"
                                   value="{{ old('tap3_content', isset($aporch) ? $aporch->tap3_content : '') }}">
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
                <a href="{{ route('admin.aporch.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush