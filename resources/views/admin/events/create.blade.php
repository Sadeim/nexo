@extends('admin.layouts.master', ['is_active_parent' => 'events','is_active'=> 'events'])
@section('title', isset($event) ? __('admin.global.edit_event') : __('admin.global.add_new_event'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.events.index') }}"
          action="{{ isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($event)
            @method('PATCH')
        @endisset

        <div class="page-content-header mb-5">
            <h2 class="table-title">{{ isset($event) ? __('admin.global.edit_event') : __('admin.global.add_new_event') }}</h2>
        </div>
        
        <!-- Sidebar: Status and Logo Image Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <!-- event Logo Section -->
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($event) && $event->image ? asset($event->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
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
        
        <!-- Main Content: event Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush">
                <div class="card-body">
                    <div class="mb-5">
                        <label class="required form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter event title"
                               value="{{ isset($event) ? $event->title : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ isset($event) ? $event->link : '' }}</textarea>
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Date</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ isset($event) ? $event->date : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Time</label>
                        <input type="time" name="time" class="form-control"
                               value="{{ isset($event) ? $event->time : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="Enter event location"
                               value="{{ isset($event) ? $event->location : '' }}">
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
                    <a href="{{ route('admin.events.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
