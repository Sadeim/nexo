@extends('admin.layouts.master', ['is_active_parent' => 'home', 'is_active' => 'skills'])
@section('title', isset($skill) ? __('admin.global.edit_skill') : __('admin.global.add_new_skill'))
@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.skills.index') }}"
          action="{{ isset($skill) ? route('admin.skills.update', $skill->id) : route('admin.skills.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($skill)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($skill) ? __('admin.global.edit_skill') : __('admin.global.add_new_skill') }}</h2>
        </div>

        <!-- Sidebar: Images Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Skill Images</h3>
                </div>
                <div class="card-body pt-0 d-flex flex-column gap-4">
                    
                    <!-- Image 1 -->
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                        <div class="image-input-wrapper w-100px h-100px" style="background-image: url({{ isset($skill) && $skill->image ? asset($skill->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>

                    <!-- Image 2 -->
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                        <div class="image-input-wrapper w-100px h-100px" style="background-image: url({{ isset($skill) && $skill->image2 ? asset($skill->image2) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="image2" accept=".png, .jpg, .jpeg" />
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>

                    <!-- Image 3 -->
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                        <div class="image-input-wrapper w-100px h-100px" style="background-image: url({{ isset($skill) && $skill->image3 ? asset($skill->image3) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="image3" accept=".png, .jpg, .jpeg" />
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Main Content: Skill Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <!-- Title Field -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Title</label>
                                <input type="text" name="title" class="form-control" placeholder=""
                                       value="{{ isset($skill) ? $skill->title : '' }}" required>
                            </div>
                        </div>
                        <!-- Description Field -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Description</label>
                                <input type="text" name="description" class="form-control" placeholder=""
                                       value="{{ isset($skill) ? $skill->description : '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Percent 1 -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Percent 1</label>
                                <input type="number" name="percent1" class="form-control" placeholder="Enter Skill Percentage"
                                       value="{{ isset($skill) ? $skill->percent1 : '' }}" required min="0" max="100">
                            </div>
                        </div>
                        <!-- Text 1 -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Text 1</label>
                                <input type="text" name="text1" class="form-control" placeholder="Enter Skill Text"
                                       value="{{ isset($skill) ? $skill->text1 : '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Percent 2 -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Percent 2</label>
                                <input type="number" name="percent2" class="form-control" placeholder="Enter Skill Percentage"
                                       value="{{ isset($skill) ? $skill->percent2 : '' }}" required min="0" max="100">
                            </div>
                        </div>
                        <!-- Text 2 -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Text 2</label>
                                <input type="text" name="text2" class="form-control" placeholder="Enter Skill Text"
                                       value="{{ isset($skill) ? $skill->text2 : '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Percent 3 -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Percent 3</label>
                                <input type="number" name="percent3" class="form-control" placeholder="Enter Skill Percentage"
                                       value="{{ isset($skill) ? $skill->percent3 : '' }}" required min="0" max="100">
                            </div>
                        </div>
                        <!-- Text 3 -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Text 3</label>
                                <input type="text" name="text3" class="form-control" placeholder="Enter Skill Text"
                                       value="{{ isset($skill) ? $skill->text3 : '' }}" required>
                            </div>
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
                <a href="{{ route('admin.skills.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush