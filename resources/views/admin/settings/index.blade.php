@extends('admin.layouts.master', ['is_active_parent' => 'settings', 'is_active' => 'settings'])
@section('title')
    {{ __('admin.form.settings') }}
@endsection
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-custom card-stretch gutter-b">
                            <div class="card card-custom">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title fs-3 fw-bold">Website Settings</div>
                                    </div>
                                    <form id="kt_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="{{ route('admin.settings.update') }}" method="POST" data-kt-redirect="{{ route('admin.settings.index') }}" enctype='multipart/form-data' novalidate="novalidate">
                                        @csrf
                                        <div class="card-body p-9">
                                            <div class="row mb-5">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Website Logo</div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="image-input image-input-outline" data-kt-image-input="true"
                                                        style="background-image: url('assets/media/svg/files/blank-image.svg')">
                                                        <div class="image-input-wrapper w-125px h-125px bgi-position-center"
                                                            style="background-size: 75%; background-image: url({{ $settings->valueOf('company_logo') !== null ? $settings->valueOf('company_logo'):asset('admin_assets/media/svg/brand-logos/volicity-9.svg') }})">
                                                        </div>
                                                        <label
                                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                            data-kt-initialized="1">
                                                            <i class="bi bi-pencil-fill fs-7"></i>
                                                            <input type="file" name="company_logo" accept=".png, .jpg, .jpeg">
                                                        </label>
                                                        <span
                                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                            data-kt-initialized="1">
                                                            <i class="bi bi-x fs-2"></i>
                                                        </span>
                                                        <span
                                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                            data-kt-initialized="1">
                                                            <i class="bi bi-x fs-2"></i>
                                                        </span>
                                                    </div>
                                                    <div class="form-text">Allowed formats: png, jpg, jpeg.</div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Email</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="email" value="{{ $settings->valueOf('email') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Phone</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="phone" value="{{ $settings->valueOf('phone') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">WhatsApp Number</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="whatsapp" value="{{ $settings->valueOf('whatsapp') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">LinkedIn</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="linkedin" value="{{ $settings->valueOf('linkedin') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Facebook</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="facebook" value="{{ $settings->valueOf('facebook') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Instagram</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="instagram" value="{{ $settings->valueOf('instagram') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Twitter</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="twitter" value="{{ $settings->valueOf('twitter') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Geographical Location</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="address" value="{{ $settings->valueOf('address') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Website URL</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="web_address" value="{{ $settings->valueOf('web_address') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row mb-8">
                                                <div class="col-xl-3">
                                                    <div class="fs-6 fw-semibold mt-2 mb-3">Map Embed</div>
                                                </div>
                                                <div class="col-xl-9 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control" name="map_embed" value="{{ $settings->valueOf('map_embed') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                                            <button type="reset" class="btn btn-light btn-active-light-primary me-2">Cancel</button>
                                            <button type="submit" class="btn btn-primary" id="kt_submit">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
