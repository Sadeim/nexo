<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{-- viewport-fit=cover lets the safe-area insets in portal.css do their job
         on notched iPhones, where the tab bar would otherwise sit under the
         home indicator. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'My Portal') — Nexo Barbers</title>

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('portal_assets/manifest.webmanifest') }}">
    <meta name="theme-color" content="#283326">

    {{-- iOS Safari ignores the manifest for "Add to Home Screen" and reads
         these instead. Without them the installed app opens in a browser
         chrome with a screenshot for an icon. --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nexo">
    <link rel="apple-touch-icon" href="{{ asset('portal_assets/icons/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('portal_assets/css/portal.css') }}">
</head>
<body class="@yield('body_class')">

    @yield('body')

    <script src="{{ asset('portal_assets/js/portal.js') }}" defer></script>
</body>
</html>
