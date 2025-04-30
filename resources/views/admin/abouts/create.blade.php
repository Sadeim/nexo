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
            <!-- Image3 -->
            <div class="card card-flush">
                <div class="card-header justify-content-center">
                    <h3 class="card-title">image3</h3>
                </div>
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" 
                                 style="background-image: url({{ isset($about) && $about->image3 ? asset($about->image3) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                            </div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Image3">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image3" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Image3">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove Image3">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Circle Text Image -->
            <div class="card card-flush">
                <div class="card-header justify-content-center">
                    <h3 class="card-title">circle_text_image</h3>
                </div>
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                 style="background-image: url({{ isset($about) && $about->circle_text_image ? asset($about->circle_text_image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Circle Text Image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="circle_text_image" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Circle Text Image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Logo Icon -->
            <div class="card card-flush">
                <div class="card-header justify-content-center">
                    <h3 class="card-title">logo_icon</h3>
                </div>
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                 style="background-image: url({{ isset($about) && $about->logo_icon ? asset($about->logo_icon) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Logo Icon">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="logo_icon" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Logo Icon">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Author Image -->
            <div class="card card-flush">
                <div class="card-header justify-content-center">
                    <h3 class="card-title">author_image</h3>
                </div>
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                 style="background-image: url({{ isset($about) && $about->author_image ? asset($about->author_image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Author Image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="author_image" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Author Image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Signature Image -->
            <div class="card card-flush">
                <div class="card-header justify-content-center">
                    <h3 class="card-title">signature_image</h3>
                </div>
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                 style="background-image: url({{ isset($about) && $about->signature_image ? asset($about->signature_image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Signature Image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="signature_image" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel Signature Image">
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
                    <!-- Row 1: Company Name and Sub Title -->
                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Company name</label>
                                <input type="text" name="company_name" class="form-control" placeholder="Enter Company Name"
                                    value="{{ isset($about) ? $about->company_name : old('company_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Sub title</label>
                                <input type="text" name="sub_title" class="form-control" placeholder="Enter Sub Title"
                                    value="{{ isset($about) ? $about->sub_title : old('sub_title') }}">
                            </div>
                        </div>
                    </div>
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
                                <label class="form-label">Check1</label>
                                <input type="text" name="check1" class="form-control" placeholder="Enter first check"
                                    value="{{ isset($about) ? $about->check1 : old('check1') }}">
                            </div>
                        </div>
                        <div class="col-md-4 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Check2</label>
                                <input type="text" name="check2" class="form-control" placeholder="Enter second check"
                                    value="{{ isset($about) ? $about->check2 : old('check2') }}">
                            </div>
                        </div>
                        <div class="col-md-4 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Check3</label>
                                <input type="text" name="check3" class="form-control" placeholder="Enter third check"
                                    value="{{ isset($about) ? $about->check3 : old('check3') }}">
                            </div>
                        </div>
                    </div>
                    <!-- Row 4: Author Info -->
                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Author name</label>
                                <input type="text" name="author_name" class="form-control" placeholder="Enter Author Name"
                                    value="{{ isset($about) ? $about->author_name : old('author_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="form-label">Author position</label>
                                <input type="text" name="author_position" class="form-control" placeholder="Enter Author Position"
                                    value="{{ isset($about) ? $about->author_position : old('author_position') }}">
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
