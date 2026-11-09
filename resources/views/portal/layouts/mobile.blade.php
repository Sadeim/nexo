{{-- Phone chrome: a sticky header and a bottom tab bar. --}}
<!doctype html>
<html lang="en">
<head>
    @include('portal.layouts.head')
</head>
<body class="v-mobile">

    <header class="p-header">
        <div>
            <h1>@yield('heading')</h1>
            @hasSection('meta')
                <div class="p-sub">@yield('meta')</div>
            @endif
        </div>
        @yield('actions')
    </header>

    <main class="p-main">
        @includeWhen(session('portal_status'), 'portal.partials.flash')
        @yield('content')
    </main>

    @include('portal.partials.tabbar')

    <script src="{{ asset('portal_assets/js/portal.js') }}" defer></script>
</body>
</html>
