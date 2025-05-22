<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->

<head>
    <base href="../../../">
    <title>House of corned beef</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 100,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue, Asp.Net Core, Blazor, Django, Flask &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
    <meta name="keywords"
        content="Metronic, Bootstrap, Bootstrap 5, Angular, VueJs, React, Asp.Net Core, Blazor, Django, Flask &amp; Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular, Asp.Net Core, Blazor, Django, Flask &amp; Laravel Admin Dashboard Theme" />
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="Keenthemes | Metronic" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="{{ asset('admin_assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ asset('admin_assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <!-------------------------------------Custom Css File----------------------------->
    <link href="{{ asset('admin_assets/css/style.css') }}" rel="stylesheet" type="text/css" />
    <!--------------------------------------------------------------------------------->
</head>
<!--end::Head-->
<!--begin::Body-->

<body data-kt-name="metronic" id="kt_body" class="app-blank">
    <!--begin::Theme mode setup on page load-->
    <script>
        if (document.documentElement) {
            const defaultThemeMode = "system";
            const name = document.body.getAttribute("data-kt-name");
            let themeMode = localStorage.getItem("kt_" + (name !== null ? name + "_" : "") + "theme_mode_value");
            if (themeMode === null) {
                if (defaultThemeMode === "system") {
                    themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            document.documentElement.setAttribute("data-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - New password -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 order-2 order-lg-2">

                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px formMobile">

                        <!--begin::Form-->
                        <form class="form w-100 " novalidate="novalidate" id="kt_new_password_form"
                            action="{{ route('admin.confirmNewPassword') }}"
                            data-kt-redirect-url="{{ route('admin.login') }}">
                            <!--begin::Heading-->
                            <div class="text-center mb-10 form-heading">
                                <!--begin::Title-->
                                <h1 class="text-dark fw-bolder mb-3">{{ __('auth.email.reset_link') }}</h1>
                                <!--end::Title-->
                                <!--begin::Link-->

                                <!--end::Link-->
                            </div>
                            <!--begin::Heading-->
                            <!-----------------------------------------Form Content------------------------>
                            <div class="form-content">
                                <!--begin::Input group-->
                                <div class="fv-row mb-8" data-kt-password-meter="true">
                                    <!--begin::Wrapper-->
                                    <div class="mb-1">
                                        <!--begin::Input wrapper-->
                                        <div class="position-relative mb-3 recoverpasword">
                                            <input class="form-control bg-transparent" type="password"
                                                placeholder="{{ __('auth.admin.password') }}" name="password"
                                                autocomplete="off" />
                                            <span
                                                class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                                data-kt-password-meter-control="visibility">
                                                <i class="bi bi-eye-slash fs-2"></i>
                                                <i class="bi bi-eye fs-2 d-none"></i>
                                            </span>
                                        </div>
                                        <!--end::Input wrapper-->
                                        <!--begin::Meter-->
                                        <div class="d-flex align-items-center mb-3 passwordStrength"
                                            data-kt-password-meter-control="highlight">
                                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                            </div>
                                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                            </div>
                                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                            </div>
                                            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                        </div>
                                        <!--end::Meter-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Input group=-->
                                <!--end::Input group=-->
                                <div class="fv-row mb-8" data-kt-password-meter="true">
                                    <!--begin::Repeat Password-->
                                    <div class="position-relative mb-3 recoverpasword">
                                        <input type="password" placeholder="{{ __('auth.admin.repeat_password') }}"
                                            name="confirm-password" autocomplete="off"
                                            class="form-control bg-transparent" />
                                        <span
                                            class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                            data-kt-password-meter-control="visibility">
                                            <i class="bi bi-eye-slash fs-2"></i>
                                            <i class="bi bi-eye fs-2 d-none"></i>
                                        </span>
                                    </div>
                                    <!--end::Repeat Password-->
                                </div>
                                <input type="hidden" name="remember_token" value="{{ $client->remember_token }}">
                                <!--end::Input group=-->


                                <!--begin::Action-->
                                <div class="d-grid mb-10">
                                    <button type="button" id="kt_new_password_submit" class="btn btn-primary">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label">{{ __('auth.admin.save') }}</span>
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span class="indicator-progress">{{ __('admin.form.please_wait') }}
                                            <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        <!--end::Indicator progress-->
                                    </button>
                                </div>
                                <!--end::Action-->
                            </div>
                            <!------------------------------------------form Content----------------------->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->

            </div>
            <!--end::Body-->
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-1"
                style="background:#48A8A6;">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-5 px-5 px-md-15 w-100 our-style">
                    <!--begin::Logo-->
                    <a href="#" class="">
                        <img alt="Logo" src="{{ asset('admin_assets/media/our-image/logo.png') }}" class="h-130px">
                    </a>
                    <!--end::Logo-->
                    <img class="mx-auto w-275px w-md-50 w-xl-500px pt-lg-20 authanticationImage"
                        src="{{ asset('admin_assets/media/our-image/reset-password.png') }}" alt="">

                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->
        </div>
        <!--end::Authentication - New password-->
    </div>
    <!--end::Root-->
    <!--end::Main-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "/";
    </script>
    <!--begin::Global Javascript Bundle(used by all pages)-->
    <script src="{{ asset('admin_assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    <script src="{{ asset('admin_assets/js/axios.min.js') }}"></script>
    <!--end::Custom Javascript-->
    <script>
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <!--begin::Custom Javascript(used by this page)-->
    <script src="{{ asset('admin_assets/js/custom/authentication/reset-password/new-password.js') }}"></script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
