{{-- Desk chrome: a fixed sidebar carrying the barber's identity and the same
     four destinations as the phone's tab bar, with the page beside it. --}}
<!doctype html>
<html lang="en">
<head>
    @include('portal.layouts.head')
</head>
<body class="v-desktop">

    <div class="d-shell">

        <aside class="d-sidebar">
            <div class="d-mark">Nexo</div>

            @if ($portalUser)
                <div class="d-who">
                    @if ($portalUser->photo_path)
                        <img class="d-avatar" src="{{ asset($portalUser->photo_path) }}" alt="">
                    @else
                        {{-- No photo on file: initials rather than a broken frame. --}}
                        <div class="d-avatar d-avatar-blank">{{ Str::substr($portalUser->name, 0, 1) }}</div>
                    @endif
                    <div class="d-who-text">
                        <div class="d-who-name">{{ $portalUser->name }}</div>
                        <div class="d-who-title">{{ $portalUser->title ?: 'Barber' }}</div>
                    </div>
                </div>
            @endif

            <nav class="d-nav">
                @foreach ([
                    'today' => 'Today',
                    'bookings' => 'Bookings',
                    'earnings' => 'Earnings',
                    'profile' => 'Profile',
                ] as $screen => $label)
                    <a href="{{ $portalRoute($screen) }}"
                       class="{{ $portalScreen === $screen ? 'on' : '' }}">{{ $label }}</a>
                @endforeach
            </nav>

            <form class="d-logout" method="POST" action="{{ $portalRoute('logout') }}">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </aside>

        <div class="d-content">
            <header class="d-header">
                <div>
                    <h1>@yield('heading')</h1>
                    @hasSection('meta')
                        <div class="d-sub">@yield('meta')</div>
                    @endif
                </div>
                <div class="d-header-actions">@yield('desktop_actions')</div>
            </header>

            <main class="d-main">
                @includeWhen(session('portal_status'), 'portal.partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('portal_assets/js/portal.js') }}" defer></script>
</body>
</html>
