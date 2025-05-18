@extends('admin.layouts.master', ['is_active_parent' => 'home', 'is_active' => 'reasons'])
@section('title', isset($reason) ? __('admin.global.edit_reason') : __('admin.global.add_new_reason'))
@section('content')

    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm"
          data-kt-redirect="{{ route('admin.reasons.index') }}"
          action="{{ isset($reason) ? route('admin.reasons.update', $reason->id) : route('admin.reasons.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @isset($reason)
            @method('PATCH')
        @endisset

        <!-- Page Header -->
        <div class="page-content-header">
            <h2 class="table-title">
                {{ isset($reason) ? __('admin.global.edit_reason') : __('admin.global.add_new_reason') }}
            </h2>
        </div>

        


        <!-- Main Content: Reason Fields -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">Reason Details</h3>
                </div>
                <div class="card-body pt-0">

                    <!-- Reason 1 -->
                    <div class="row align-items-end mb-8 border-bottom pb-6">
                        <div class="col-md-3 fv-row">
                            <label class="form-label">Icon 1</label>
                            <input type="text" name="icon" class="form-control"
                                   value="{{ old('icon', isset($reason) ? $reason->icon : '') }}"
                                   placeholder="e.g. fas fa-star">
                        </div>
                        <div class="col-md-3 fv-row">
                            <label class="form-label">Title 1</label>
                            <input type="text" name="title" class="form-control"
                                   value="{{ old('title', isset($reason) ? $reason->title : '') }}"
                                   placeholder="Enter title">
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="form-label">Description 1</label>
                            <input type="text" name="text" class="form-control"
                                   value="{{ old('text', isset($reason) ? $reason->text: '') }}"
                                   placeholder="Enter description">
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
                <a href="{{ route('admin.reasons.index') }}" class="btn btn-light me-5 cancel">
                    {{ __('admin.form.cancel') }}
                </a>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush