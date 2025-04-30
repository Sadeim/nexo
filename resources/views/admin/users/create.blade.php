@extends('admin.layouts.master', ['is_active_parent' => 'user_management','is_active'=> 'users'])
@section('title')
    {{ __('admin.global.add_new_user') }}
@endsection
@section('content')
    <form id="kt_form" class="form" data-kt-redirect="{{ route('admin.users.index') }}"
            action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
        @csrf
        @isset($user)
            @method('PATCH')
        @endif

        <div class="">
            <div class="page-content-header">
                <h2 class="table-title">{{ __('admin.global.add_new_user') }}</h2>
            </div>
            <div class="card card-flush">
                <div class="card-body">
                    <div class="new-user-form" id="new-user-form">
                        <div class="formContent">
                            <div class="row g-9 mb-7">
                                <div class="fv-row col-md-6">
                                    <label class="fs-6 fw-semibold mb-2">
                                        <span class="required">{{ __('admin.form.first_name') }}</span>
                                    </label>
                                    <input type="text" class="form-control form-control-solid"
                                        placeholder="" name="first_name" value="{{ isset($user) ? $user->first_name : '' }}">
                                </div> 
                                <div class="fv-row col-md-6">
                                    <label class="fs-6 fw-semibold mb-2">
                                        <span class="required">{{ __('admin.form.last_name') }}</span>
                                    </label>
                                    <input type="text" class="form-control form-control-solid"
                                        placeholder="" name="last_name" value="{{ isset($user) ? $user->last_name : '' }}">
                                </div> 
                            </div>
                            
                            <div class="row g-9 mb-7">
                                <div class="fv-row col-md-6">
                                    <label class="fs-6 fw-semibold mb-2">
                                        <span class="required">{{ __('admin.form.phone') }}</span>
                                    </label>
                                    <input type="hidden" class="fade" id="phone_code" name="phone_code" 
                                               value="{{ isset($user) ? $user->phone_code : '' }}" readonly>
                                    <div>
                                        <input type="tel" class="form-control form-control-solid" id="phone" 
                                               placeholder="596123456" name="phone" value="{{ isset($user) ? $user->phone : '' }}">
                                    </div>
                                </div>
                                <div class="fv-row col-md-6">
                                    <label class="fs-6 fw-semibold mb-2">
                                        <span class="required">{{ __('admin.form.email') }}</span>
                                    </label>
                                    <input type="email" class="form-control form-control-solid"
                                        placeholder="" name="email" value="{{ isset($user) ? $user->email : '' }}">
                                </div>
                            </div>
                            
                            <div class="row g-9 mb-7">
                               
                                <div class="fv-row col-md-6">
                                    <label class="fs-6 fw-semibold mb-2">
                                        <span class="required">{{ __('admin.form.password') }}</span>
                                    </label>
                                    <input type="text" class="form-control form-control-solid"
                                        placeholder="" name="password" value="">
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
                            <span class="indicator-label">{{ __('admin.form.save') }}</span>
                            <span class="indicator-progress">{{ __('admin.form.please_wait') }}
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <a href="{{ route('admin.users.index') }}" id="kt_ecommerce_add_product_cancel"
                            class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" integrity="sha512-H9j+XLVjDv6wSQCEW6pUvBy05BZHDHs2OCcJkQZp/8iHVQscn6pyM+2iLyvFxX9T2Uw7+YzPQuw+YUXwKJ5m0A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('scripts')
    <!-- intl-tel-input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"   crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
    <script>
        // تهيئة intl-tel-input على حقل الهاتف
        var phoneInput = document.querySelector("#phone");
        var iti = window.intlTelInput(phoneInput, {
            separateDialCode: false, // إذا أردت دمج رمز الدولة داخل الحقل
            initialCountry: "auto",
            initialCountry: "sa",
             
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js" // تحميل ملف utils.js
        });

        // عند تغيير رقم الهاتف، يتم تحديث حقل رمز الهاتف
        phoneInput.addEventListener("countrychange", function() {
            var dialCode = iti.getSelectedCountryData().dialCode;
            document.querySelector("#phone_code").value = dialCode;
        });
        // في حالة تحميل الصفحة وتوجد قيمة مسبقة
        document.addEventListener("DOMContentLoaded", function() {
            var dialCode = iti.getSelectedCountryData().dialCode;
            document.querySelector("#phone_code").value = dialCode;
        });
    </script>
    <style>
        .iti{
            width: 100% !important;
        }
    </style>
@endpush
