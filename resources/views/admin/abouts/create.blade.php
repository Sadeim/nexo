@extends('admin.layouts.master', ['is_active_parent' => 'abouts', 'is_active' => 'abouts'])
@section('title', isset($about) ? __('admin.global.edit_about') : __('admin.global.add_new_about'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm"
          data-kt-redirect="{{ route('admin.abouts.index') }}"
          action="{{ isset($about) ? route('admin.abouts.update', $about->id) : route('admin.abouts.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @isset($about)
            @method('PATCH')
        @endisset

        <!-- Page Header -->
        <div class="page-content-header">
            <h2 class="table-title">{{ isset($about) ? __('admin.global.edit_about') : __('admin.global.add_new_about') }}</h2>
        </div>

        <!-- Sidebar: Image Uploaders -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <!-- Image1 -->
            <div class="card card-flush">
                <div class="card-header justify-content-center">
                    <h3 class="card-title">Image1</h3>
                </div>
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                 style="background-image: url({{ isset($about) && $about->image1 ? asset($about->image1) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                            </div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Image1">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image1" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Image1">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove Image1">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Image2 -->
            <div class="card card-flush">
                <div class="card-header justify-content-center">
                    <h3 class="card-title">image2</h3>
                </div>
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                 style="background-image: url({{ isset($about) && $about->image2 ? asset($about->image2) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                            </div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Image2">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image2" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Image2">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove Image2">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content: About Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="salesTitle">
                    <h3>{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">

                    <!-- Row 2: Title and Description -->
                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter Title"
                                    value="{{ isset($about) ? $about->title : old('title') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Enter Description">{{ isset($about) ? $about->description : old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Row 3: Check Fields -->
                    <div class="row">
                        <div class="col-md-4 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Check1 Title</label>
                                <input type="text" name="tab1_title" class="form-control" placeholder="Enter first check"
                                    value="{{ isset($about) ? $about->tab1_title : old('tab1_title') }}">
                            </div>
                        </div>
                        <div class="col-md-4 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Check2 Title</label>
                                <input type="text" name="tab2_title" class="form-control" placeholder="Enter second check"
                                    value="{{ isset($about) ? $about->tab2_title : old('tab2_title') }}">
                            </div>
                        </div>
                    </div>
                    <!-- Row 4: Author Info -->

                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Chech1 Content</label>
                                <input type="text" name="tab1_content" class="form-control" placeholder="Enter Author Name"
                                       value="{{ isset($about) ? $about->tab1_content : old('tab1_content') }}">
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Chech2 Content</label>
                                <input type="text" name="tab2_content" class="form-control" placeholder="Enter Author Position"
                                       value="{{ isset($about) ? $about->tab2_content : old('tab2_content') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Button Text</label>
                                <input type="text" name="button_text" class="form-control" placeholder="Enter Author Name"
                                       value="{{ isset($about) ? $about->button_text : old('button_text') }}">
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Button Link</label>
                                <input type="text" name="button_link" class="form-control" placeholder="Enter Author Position"
                                       value="{{ isset($about) ? $about->button_link : old('button_link') }}">
                            </div>
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
                    <a href="{{ route('admin.abouts.index') }}" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5 cancel">
                        {{ __('admin.form.cancel') }}
                    </a>
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
            placeholder: '{{ __('admin.global.type_your_text_here') }}',
            tabsize: 2,
            height: 150,
            lang: 'en-US',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>
@endpush
