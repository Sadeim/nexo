@extends('admin.layouts.master', ['is_active_parent' => 'testimonials', 'is_active' => 'testimonials'])
@section('title', isset($testimonial) ? __('admin.global.edit_testimonial') : __('admin.global.add_new_testimonial'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm"
        data-kt-redirect="{{ route('admin.testimonials.index') }}"
        action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial->id) : route('admin.testimonials.store') }}"
        method="POST">
        @csrf
        @isset($testimonial)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">Testimonial details</h2>
        </div>

        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            {{-- <div class="card card-flush">
            <div class="card-header">
                <div class="card-title">
                    <h3>{{ __('admin.form.status') }}</h3>
                </div>
                <div class="card-toolbar">
                    <label class="form-check form-switch form-check-custom form-check-solid">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input btn active_operation" type="checkbox"
                            name="status" @if (isset($testimonial) && $testimonial->status == 1) checked @endif value="1">
                    </label>
                </div>
            </div>
        </div> --}}
            <div class="card card-flush">
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                style="background-image: url({{ isset($testimonial) && $testimonial->photo ? asset($testimonial->photo) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                            </div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-5">
                        <label class=" form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $testimonial->name ?? '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control"
                            value="{{ isset($testimonial) ? $testimonial->position : ''  }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" value="{{ isset($testimonial) ? $testimonial->message : '' }}">{{ isset($testimonial) ? $testimonial->message : '' }}</textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Rating</label>
                        <input type="text" name="rating" class="form-control"
                            value="{{ isset($testimonial) ? $testimonial->rating : '' }}">
                    </div>
                </div>
            </div>
        </div>


        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end">
                    <button id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.testimonials.index') }}"
                        class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
