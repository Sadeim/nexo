@extends('admin.layouts.master', ['is_active_parent' => 'home', 'is_active' => 'reasons'])
@section('title', isset($reason) ? __('admin.global.edit_reason') : __('admin.global.add_new_reason'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.reasons.index') }}"
          action="{{ isset($reason) ? route('admin.reasons.update', $reason->id) : route('admin.reasons.store') }}" method="POST">
        @csrf
        @isset($reason)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($reason) ? __('admin.global.edit_reason') : __('admin.global.add_new_reason') }}</h2>
        </div>

        <!-- Sidebar: Status Section -->
        {{-- <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>{{ __('admin.form.status') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input btn active_operation" type="checkbox" name="status"
                                   @if(isset($reason) && $reason->status == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Main Content: Reason Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-5">
                        <label class=" form-label">Icon</label>
                        <input type="text" name="icon" class="form-control" placeholder="Enter Icon Class"
                               value="{{ isset($reason) ? $reason->icon : old('icon') }}" >
                    </div>
                    <div class="mb-5">
                        <label class=" form-label">{{ __('admin.form.title') }}</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter Reason Title"
                               value="{{ isset($reason) ? $reason->title : old('title') }}" >
                    </div>
                    <div class="mb-5">
                        <label class="form-label">{{ __('admin.form.description') }}</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Enter Reason Description">{{ isset($reason) ? $reason->description : old('description') }}</textarea>
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
                    <a href="{{ route('admin.reasons.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
