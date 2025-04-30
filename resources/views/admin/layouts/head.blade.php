<head>
    <base href="">
    <title>@yield('title')</title>
    <meta name="_token" content="{{ csrf_token() }}">
    <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}">
    <meta charset="utf-8" />
    <meta name="description" content=" " />
    <meta name="keywords" content=" " />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content=" " />
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="Keenthemes | Metronic" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="{{ $settings->valueOf('company_logo') }}" type="image/x-icon" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used by this page)-->
    <link href="{{ asset('admin_assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin_assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ asset('admin_assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets/css/summernote-lite.min.css') }}" rel="stylesheet">

    <!--end::Global Stylesheets Bundle-->
    <!-------------------------------------Custom Css File----------------------------->

    <link href="{{ asset('admin_assets/css/intlTelInput.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets/css/style.css') }}" rel="stylesheet" type="text/css" />

    <!--------------------------------------------------------------------------------->
    @stack('style')
</head>
