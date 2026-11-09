@extends('portal.layout')
@section('title', 'Bookings')

@section('body')
    <header class="p-header">
        <div>
            <h1>Bookings</h1>
            <div class="p-sub">{{ $bookings->total() }} {{ Str::plural('booking', $bookings->total()) }}</div>
        </div>
    </header>

    <main class="p-main">
        <div class="p-tabs">
            <a class="p-tab {{ $tab === 'upcoming' ? 'p-tab-on' : '' }}"
               href="{{ route('portal.bookings') }}">Upcoming</a>
            <a class="p-tab {{ $tab === 'past' ? 'p-tab-on' : '' }}"
               href="{{ route('portal.bookings', ['tab' => 'past']) }}">Past</a>
        </div>

        @forelse ($bookings as $booking)
            @include('portal.partials.booking-card', ['booking' => $booking, 'showDate' => true])
        @empty
            <div class="p-empty">
                <div class="p-empty-title">{{ $tab === 'past' ? 'No history yet' : 'Nothing coming up' }}</div>
                {{ $tab === 'past'
                    ? 'Appointments you have finished will show here.'
                    : 'You have no upcoming appointments.' }}
            </div>
        @endforelse

        {{-- Hand-rolled rather than $bookings->links(): Laravel's paginator
             views are Tailwind/Bootstrap markup, and the portal has neither. --}}
        @if ($bookings->hasPages())
            <div class="p-pager">
                @if ($bookings->onFirstPage())
                    <span>Newer</span>
                @else
                    <a href="{{ $bookings->previousPageUrl() }}" rel="prev">Newer</a>
                @endif

                @if ($bookings->hasMorePages())
                    <a href="{{ $bookings->nextPageUrl() }}" rel="next">Older</a>
                @else
                    <span>Older</span>
                @endif
            </div>
        @endif
    </main>

    @include('portal.partials.tabbar')
@endsection
