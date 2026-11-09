{{-- Bottom tab bar, phones only. Four destinations, always the same four, so
     the portal never changes shape under a barber depending on their data.
     The desktop sidebar carries the same list. --}}
<nav class="p-tabbar">
    <a href="{{ $portalRoute('today') }}" class="{{ $portalScreen === 'today' ? 'on' : '' }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2v2h10V2h2v2h2v18H3V4h2V2h2zm12 8H5v10h14V10z"/></svg>
        Today
    </a>
    <a href="{{ $portalRoute('bookings') }}" class="{{ $portalScreen === 'bookings' ? 'on' : '' }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h18v3H3V4zm0 6h18v3H3v-3zm0 6h12v3H3v-3z"/></svg>
        Bookings
    </a>
    <a href="{{ $portalRoute('earnings') }}" class="{{ $portalScreen === 'earnings' ? 'on' : '' }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1a11 11 0 100 22 11 11 0 000-22zm1 17h-2v-1.6a4 4 0 01-3-2.4l1.8-.8A2.2 2.2 0 0012 14.4c1 0 1.8-.5 1.8-1.3 0-.8-.7-1.1-2.2-1.6-1.5-.5-3.1-1.1-3.1-3 0-1.5 1.1-2.6 2.5-3V4h2v1.5a3.8 3.8 0 012.7 2.2l-1.8.8A2 2 0 0012 7.4c-1 0-1.6.5-1.6 1.1 0 .8.8 1 2.2 1.5 1.7.6 3.1 1.3 3.1 3.1 0 1.7-1.2 2.8-2.7 3.2V18z"/></svg>
        Earnings
    </a>
    <a href="{{ $portalRoute('profile') }}" class="{{ $portalScreen === 'profile' ? 'on' : '' }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.4 0-8 2.2-8 5v3h16v-3c0-2.8-3.6-5-8-5z"/></svg>
        Profile
    </a>
</nav>
