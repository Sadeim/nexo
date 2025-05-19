@extends('admin.layouts.master', ['is_active_parent' => 'contacts', 'is_active' => 'contacts'])
@section('title', isset($contact) ? __('admin.global.edit_contact') : __('admin.global.add_new_contact'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.contacts.index') }}"
          action="{{ isset($contact) ? route('admin.contacts.update', $contact->id) : route('admin.contacts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($contact)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($contact) ? __('admin.global.edit_contact') : __('admin.global.add_new_contact') }}</h2>
        </div>

        <!-- Sidebar: Status and Image Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            {{-- <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>{{ __('admin.form.status') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input btn active_operation" type="checkbox" name="status" 
                                @if(isset($contact) && $contact->status == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div> --}}
            <!-- contact Image Section -->
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($contact) && $contact->image ? asset($contact->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
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

        <!-- Main Content: contact Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-5">
                        <label class=" form-label">{{ __('admin.form.name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter contact Member Name"
                               value="{{ isset($contact) ? $contact->name : '' }}" >
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" placeholder="Enter Position"
                               value="{{ isset($contact) ? $contact->position : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Social links (JSON format)</label>
                        <textarea name="social_links" class="form-control" rows="3" placeholder='{"facebook":"https://facebook.com", "twitter":"https://twitter.com"}'>{{ isset($contact) && $contact->social_links ? json_encode($contact->social_links) : '' }}</textarea>
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
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
