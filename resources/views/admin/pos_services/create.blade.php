@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'pos_services'])
@section('title', isset($posService) ? 'Edit POS service' : 'Add POS service')
@section('content')
    <form id="kt_form" class="form" data-kt-redirect="{{ route('admin.pos_services.index') }}"
          action="{{ isset($posService) ? route('admin.pos_services.update', $posService->id) : route('admin.pos_services.store') }}">
        @csrf
        @isset($posService)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($posService) ? 'Edit POS service' : 'Add POS service' }}</h2>
        </div>

        <div class="card card-flush">
            <div class="card-body">
                <div class="row g-9 mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">{{ __('admin.form.name') }}</label>
                        <input class="form-control" name="name"
                               placeholder="e.g. Haircut, Beard trim"
                               value="{{ isset($posService) ? $posService->name : '' }}">
                    </div>
                    <div class="col-md-3 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Price ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control"
                               name="price" placeholder="30.00"
                               value="{{ isset($posService) ? number_format((float) $posService->price, 2, '.', '') : '' }}">
                    </div>
                    <div class="col-md-3 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Sort order</label>
                        <input type="number" min="0" class="form-control" name="sort_order"
                               placeholder="0"
                               value="{{ isset($posService) ? $posService->sort_order : 0 }}">
                    </div>
                </div>

                <div class="row g-9">
                    <div class="col-md-6 fv-row">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   {{ !isset($posService) || $posService->is_active ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold ms-2">Active (shows in POS)</span>
                        </label>
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
                    <a href="{{ route('admin.pos_services.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
