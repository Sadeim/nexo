<!DOCTYPE html>
<html>
<!--begin::Head-->
@include('admin.layouts.head')
<!--end::Head-->
<!--begin::Body-->

<body data-kt-name="metronic" id="kt_body" class="header-tablet-and-mobile-fixed aside-enabled">
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
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Aside-->
            @include('admin.layouts.aside')
            <!--end::Aside-->

            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid pt-0" id="kt_wrapper">
                <!--begin::Header-->
                @include('admin.layouts.header')
                <!--end::Header-->
                <!--begin::Content-->
                <div class="d-flex flex-column flex-column-fluid mt-5" id="kt_content">
                    <!--begin::Post-->
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <!--begin::Container-->
                        <div id="kt_content_container" class="container-fluid">
                            @yield('content')
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Post-->
                </div>
                <!--end::Content-->
                <!--begin::Footer-->
                @include('admin.layouts.footer')
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->


    <!--begin::Modals-->
    @include('admin.layouts.modal')
    <!--begin::Javascript-->
    @include('admin.layouts.scripts')
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
