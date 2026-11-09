{{-- Sign-in and password screens.
     One layout for both versions: there is no chrome to differ. Nobody is
     signed in yet, so there is no photo, no name and no navigation to draw —
     a centred form is the right answer at every width. --}}
<!doctype html>
<html lang="en">
<head>
    @include('portal.layouts.head')
</head>
<body class="is-plain v-{{ $portalVariant }}">

    <div class="p-centre">
        <div class="p-brand">Nexo</div>
        <div class="p-brand-sub">@yield('brand_sub', 'Barber portal')</div>

        @yield('content')
    </div>

    <script src="{{ \App\Support\Portal::asset('portal_assets/js/portal.js') }}" defer></script>
</body>
</html>
