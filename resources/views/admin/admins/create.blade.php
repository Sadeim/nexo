@extends('admin.layouts.master', ['is_active_parent' => 'user_management','is_active'=> 'admins'])
@section('title')
    {{ __('admin.admins.add_new_admin') }}
@endsection
@section('content')
    <form id="kt_form" class="form" data-kt-redirect="{{ route('admin.admins.index') }}"
            action="{{ isset($admin) ? route('admin.admins.update' ,  $admin->id) : route('admin.admins.store') }}">
        @csrf
        @isset($admin)
            @method('PATCH')
        @endif

        <div class="">
            <div class="page-content-header">
                <h2 class="table-title">{{ __('admin.admins.add_new_admin') }}</h2>
            </div>
            <div class="card card-flush">
                <div class="card-body">
                    <div class="new-user-form" id="new-user-form">
                        <div class="formContent">
                            <div class="row g-9 mb-7">
                                <div class=" fv-row fv-plugins-icon-container">
                                    <label class="required fs-6 fw-semibold mb-2">{{ __('admin.admins.name') }}</label>
                                    <input class="form-control" placeholder="{{ __('admin.form.name') }}" name="name" value="{{ isset($admin) ? $admin->name:'' }}">
                                </div>
                                <div class="fv-row fv-plugins-icon-container">
                                   <label class="fs-6 fw-semibold mb-2">
                                        <span class="required">{{ __('admin.form.choose_role') }}</span>
                                    </label>
                                    <select class="form-select mb-8" data-control="select2" data-placeholder="{{ isset($admin) ? $adminRole->name: __('admin.form.choose_role') }}" name="role">
                                        <option></option>
                                        @foreach ($roles as $item)
                                            <option value="{{ $item->name }}"
                                                @if(isset($admin) && $item->id == $adminRole->id) selected @endif>{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-9 mb-7">
                                <div class="mb-7 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">{{ __('admin.admins.password') }}</label>
                                    <input type="password" class="form-control" placeholder="*********" name="password" value="">
                                </div>

                                <div class="mb-7 fv-row">
                                     <label class="fs-6 fw-semibold mb-2">
                                        <span class="required">{{ __('admin.admins.email') }}</span>
                                    </label>
                                    <input type="email" class="form-control" placeholder="sean@dellito.com" name="email" value="">
                                </div>
                            </div>
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
                        <a href="{{ route('admin.admins.index') }}" id="kt_ecommerce_add_product_cancel"
                            class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
