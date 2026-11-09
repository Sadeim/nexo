@extends($portalLayout)
@section('title', 'Bookings')
@section('heading', 'Bookings')
@section('meta', $bookings->total().' '.Str::plural('booking', $bookings->total()))

@php
    // The other direction, for the sortable date column.
    $flip = $dir === 'asc' ? 'desc' : 'asc';
@endphp

@section('content')
    <div class="{{ $portalIsDesktop ? 'd-tabs' : 'p-tabs' }}">
        <a class="p-tab {{ $tab === 'upcoming' ? 'p-tab-on' : '' }}"
           href="{{ $portalRoute('bookings', $filter === 'all' ? [] : ['barber' => $filter]) }}">Upcoming</a>
        <a class="p-tab {{ $tab === 'past' ? 'p-tab-on' : '' }}"
           href="{{ $portalRoute('bookings', $filter === 'all' ? ['tab' => 'past'] : ['tab' => 'past', 'barber' => $filter]) }}">Past</a>
    </div>

    {{-- Manager only; the partial is not rendered at all for a barber. --}}
    @if ($portalIsManager)
        @include('portal.partials.barber-filter')
    @endif

    @if ($bookings->isEmpty())

        <div class="p-empty">
            <div class="p-empty-title">{{ $tab === 'past' ? 'No history yet' : 'Nothing coming up' }}</div>
            @if ($portalIsManager)
                {{ $tab === 'past'
                    ? 'Nothing matching this filter has happened yet.'
                    : 'Nothing matching this filter is booked.' }}
            @else
                {{ $tab === 'past'
                    ? 'Appointments you have finished will show here.'
                    : 'You have no upcoming appointments.' }}
            @endif
        </div>

    @elseif ($portalIsDesktop)

        <table class="d-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>
                        {{-- The one sortable column. Carries the tab and the
                             barber filter with it, so sorting does not quietly
                             drop you back to everyone's Upcoming. --}}
                        <a class="d-sort"
                           href="{{ $portalRoute('bookings', array_filter([
                                'tab' => $tab,
                                'dir' => $flip,
                                'barber' => $filter === 'all' ? null : $filter,
                           ])) }}">
                            Date {!! $dir === 'asc' ? '&uarr;' : '&darr;' !!}
                        </a>
                    </th>
                    <th>Customer</th>
                    <th>Service</th>
                    @if ($portalIsManager)
                        <th>Barber</th>
                    @endif
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    @include('portal.partials.booking-row', [
                        'booking' => $booking,
                        'showDate' => true,
                        'showBarber' => $portalIsManager,
                    ])
                @endforeach
            </tbody>
        </table>

    @else

        @foreach ($bookings as $booking)
            @include('portal.partials.booking-card', [
                'booking' => $booking,
                'showDate' => true,
                'showBarber' => $portalIsManager,
            ])
        @endforeach

    @endif

    {{-- Hand-rolled rather than $bookings->links(): Laravel's paginator views
         are Tailwind/Bootstrap markup, and the portal has neither. --}}
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
@endsection
