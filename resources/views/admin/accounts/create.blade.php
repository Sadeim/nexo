@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'accounts'])
@section('title', isset($admin) ? 'Edit user' : 'Add new user')
@section('content')
    <form id="kt_form" class="form" data-kt-redirect="{{ route('admin.accounts.index') }}"
        action="{{ isset($admin) ? route('admin.accounts.update', $admin->id) : route('admin.accounts.store') }}">
        @csrf
        @isset($admin)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($admin) ? 'Edit user' : 'Add new user' }}</h2>
        </div>

        <div class="card card-flush">
            <div class="card-body">
                <div class="row g-9 mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">{{ __('admin.form.name') }}</label>
                        <input class="form-control" name="name" placeholder="Name"
                            value="{{ isset($admin) ? $admin->name : '' }}">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Role</label>
                        <select class="form-select" name="role" data-control="select2" data-placeholder="Choose role">
                            <option></option>
                            @foreach ($roles as $item)
                                <option value="{{ $item->name }}"
                                    @if (isset($adminRole) && $adminRole && $item->id == $adminRole->id) selected @endif>
                                    {{ ucfirst($item->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-9 mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="email@example.com"
                            value="{{ isset($admin) ? $admin->email : '' }}">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">
                            {{ isset($admin) ? 'New password (leave blank to keep)' : 'Password' }}
                            @if (!isset($admin))<span class="required"></span>@endif
                        </label>
                        <input type="password" class="form-control" name="password" placeholder="*********"
                            autocomplete="new-password">
                        <div class="text-muted fs-8 mt-1">At least 8 characters, with letters and numbers.</div>
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
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
