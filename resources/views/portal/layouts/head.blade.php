{{-- Shared <head> for every portal screen, both versions. --}}
<meta charset="utf-8">
{{-- viewport-fit=cover lets the safe-area insets in portal.css do their job
     on notched iPhones, where the tab bar would otherwise sit under the
     home indicator. --}}
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">

<title>@yield('title', 'My Portal') — Nexo Barbers</title>

<meta name="theme-color" content="#283326">

@if (! $portalIsDesktop)
    {{-- Installable on phones only. /work has no manifest and no worker. --}}
    <link rel="manifest" href="{{ \App\Support\Portal::asset('portal_assets/manifest.webmanifest') }}">

    {{-- iOS Safari ignores the manifest for "Add to Home Screen" and reads
         these instead. Without them the installed app opens in browser chrome
         with a screenshot for an icon. --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nexo">
    <link rel="apple-touch-icon" href="{{ asset('portal_assets/icons/apple-touch-icon.png') }}">
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Inter:wght@400;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ \App\Support\Portal::asset('portal_assets/css/portal.css') }}">

@if ($portalIsDesktop && $portalCounterpart)
    {{-- The handover. /work is a sidebar and a table; below the desktop
         breakpoint it has nothing sensible to do, so it hands the barber to
         the /me URL for the SAME screen — query string and all — rather than
         reflowing into something that is neither.

         Inline and blocking, before anything paints, so a phone that lands on
         a /work link never flashes a desktop layout it cannot use.
         location.replace keeps the dead width out of the back history.

         Checked once, on load, and never again. There is deliberately NO
         resize listener: a barber half-way through the change-password form
         must not be thrown onto another URL — losing what they had typed —
         because they dragged a window edge or a tiling manager moved them.
         The width that matters is the one the page was opened at. --}}
    <script>
        (function () {
            if (window.innerWidth < {{ $portalMinWidth }}) {
                window.location.replace(@json($portalCounterpart));
            }
        })();
    </script>
@endif
