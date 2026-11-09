@extends('portal.layout')
@section('title', 'Today')

@section('body')
    <header class="p-header">
        <div>
            <h1>Today</h1>
            <div class="p-sub">{{ $today->format('l j F') }}</div>
        </div>
        <button class="p-refresh" type="button" data-portal-refresh>Refresh</button>
    </header>

    <main class="p-main">
        @if (session('portal_status'))
            <div class="p-flash">{{ session('portal_status') }}</div>
        @endif

        <div class="p-note">
            <strong>Unassigned today: {{ $unassigned }}</strong><br>
            {{ $unassigned === 0
                ? 'Every booking today has a barber.'
                : 'Bookings today with no barber chosen yet. Ask the front desk.' }}
        </div>

        @forelse ($bookings as $booking)
            @include('portal.partials.booking-card', ['booking' => $booking])
        @empty
            <div class="p-empty">
                <div class="p-empty-title">Nothing booked</div>
                You have no appointments today.
            </div>
        @endforelse
    </main>

    @include('portal.partials.tabbar')
@endsection
