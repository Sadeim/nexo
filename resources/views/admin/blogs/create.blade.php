@extends('admin.layouts.master', ['is_active_parent' => 'blogs','is_active'=> 'blogs'])
@section('title', isset($blog) ? __('admin.global.edit_blog') : __('admin.global.add_new_blog'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.blogs.index') }}"
          action="{{ isset($blog) ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($blog)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($blog) ? __('admin.global.edit_blog') : __('admin.global.add_new_blog') }}</h2>
        </div>

        <!-- Sidebar: Status and Image Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>{{ __('admin.form.status') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input btn active_operation" type="checkbox" name="status"
                                @if(isset($blog) && $blog->status == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div>
            <!-- Banner Image Section for Blog Post -->
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($blog) && $blog->image ? asset($blog->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
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

        <!-- Main Content: Blog Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-5">
                        <label class="required form-label">{{ __('admin.form.title') }}</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter Blog Title" value="{{ old('title', $blog->title ?? '') }}" required>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">{{ __('admin.form.category') }}</label>
                        <input type="text" name="category" class="form-control" placeholder="Enter Category" value="{{ old('category', $blog->category ?? '') }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">{{ __('admin.form.author') }}</label>
                        <input type="text" name="author" class="form-control" placeholder="Enter Author" value="{{ old('author', $blog->author ?? '') }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">{{ __('admin.form.published_at') }}</label>
                        <input type="date" name="published_at" class="form-control" value="{{ old('published_at', isset($blog->created_at) ? $blog->created_at->format('Y-m-d') : '') }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">{{ __('admin.form.content') }}</label>
                        <textarea name="content" id="summernote" class="form-control" rows="6" placeholder="Enter Blog Content">{{ old('content', $blog->content ?? '') }}</textarea>
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
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
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
            height: 200,
            lang: 'en-US',
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
@endpush
