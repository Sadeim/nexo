{{-- Phone chrome: a sticky header and a bottom tab bar. --}}
<!doctype html>
<html lang="en">
<head>
    @include('portal.layouts.head')
</head>
<body class="v-mobile">

    <header class="p-header">
        <div>
            <div class="p-heading">
                <h1>@yield('heading')</h1>
                {{-- Who the portal thinks you are. The one piece of chrome
                     that differs between a barber and the owner, and the
                     reason the screens below look different. --}}
                @if ($portalIsManager)
                    <span class="p-badge p-badge-manager">Manager</span>
                @endif
            </div>
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
