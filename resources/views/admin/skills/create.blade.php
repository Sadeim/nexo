@extends('admin.layouts.master', ['is_active_parent' => 'skills', 'is_active' => 'skills'])
@section('title', isset($skill) ? __('admin.global.edit_skill') : __('admin.global.add_new_skill'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.skills.index') }}"
          action="{{ isset($skill) ? route('admin.skills.update', $skill->id) : route('admin.skills.store') }}" method="POST">
        @csrf
        @isset($skill)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($skill) ? __('admin.global.edit_skill') : __('admin.global.add_new_skill') }}</h2>
        </div>

        {{-- <!-- Sidebar: Status Section -->
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
                                   @if(isset($skill) && $skill->status == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Main Content: Skill Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <!-- Percentage Field -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Percent</label>
                                <input type="number" name="percent" class="form-control" placeholder="Enter Skill Percentage"
                                       value="{{ isset($skill) ? $skill->percent : '' }}" required min="0" max="100">
                            </div>
                        </div>
                        <!-- Text Field -->
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Text</label>
                                <input type="text" name="text" class="form-control" placeholder="Enter Skill Text"
                                       value="{{ isset($skill) ? $skill->text : '' }}" required>
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
