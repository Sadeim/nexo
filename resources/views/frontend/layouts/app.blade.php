<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('frontend_assets/src/output.css') }}" rel="stylesheet">
    <title>{{ $settings->valueOf('site_title') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($settings->valueOf('browser_icon')) }}">



    @stack('styles')
</head>

<body>
    <!-- Include Header -->
    @include('frontend.partials.header')


    <!-- Main Content -->
    <main>

        @yield('content')

    </main>

    <!-- Include Footer -->
    @include('frontend.partials.footer')

    @stack('scripts')



</body>

</html>
