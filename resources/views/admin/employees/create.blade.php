@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'employees'])
@section('title', isset($employee) ? 'Edit employee' : 'Add new employee')
@section('content')
    <form id="kt_form" class="form" data-kt-redirect="{{ route('admin.employees.index') }}"
          enctype="multipart/form-data"
          action="{{ isset($employee) ? route('admin.employees.update', $employee->id) : route('admin.employees.store') }}">
        @csrf
        @isset($employee)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($employee) ? 'Edit employee' : 'Add new employee' }}</h2>
        </div>

        <div class="card card-flush">
            <div class="card-body">
                <div class="row g-9 mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">{{ __('admin.form.name') }}</label>
                        <input class="form-control" name="name" placeholder="Employee name"
                               value="{{ isset($employee) ? $employee->name : '' }}">
                    </div>
                    <div class="col-md-3 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Sort order</label>
                        <input type="number" min="0" class="form-control" name="sort_order"
                               placeholder="0"
                               value="{{ isset($employee) ? $employee->sort_order : 0 }}">
                        <div class="text-muted fs-8 mt-1">Lower numbers appear first in the POS panel.</div>
                    </div>
                    <div class="col-md-3 fv-row d-flex align-items-end">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   {{ !isset($employee) || $employee->is_active ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold ms-2">Active</span>
                        </label>
                    </div>
                </div>

                <div class="row g-9">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Avatar (optional)</label>
                        <input type="file" name="avatar" accept="image/*" class="form-control">
                        @if (isset($employee) && $employee->avatar)
                            <div class="mt-3">
                                <img src="{{ asset($employee->avatar) }}"
                                     class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end right">
                    <button type="submit" id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
