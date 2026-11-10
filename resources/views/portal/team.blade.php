@extends($portalLayout)
@section('title', 'All Bookings')
@section('heading', 'All Bookings')
@section('meta', $day->format('l j F') . ' · ' . $bookings->count() . ' ' . Str::plural('booking', $bookings->count()))

{{-- The day being shown, and the way to move off it. Rendered into both
     chromes' action slots so the arrows sit in the header on a phone and in
     the header on the desk, rather than floating above the content. --}}
@php
    $dayNav = [
        'prev' => $portalRoute('team', array_filter(['date' => $previousDay, 'barber' => $filter === 'all' ? null : $filter])),
        'next' => $portalRoute('team', array_filter(['date' => $nextDay, 'barber' => $filter === 'all' ? null : $filter])),
        'today' => $portalRoute('team', array_filter(['barber' => $filter === 'all' ? null : $filter])),
    ];
@endphp

@section('actions')
    <div class="p-daynav">
        <a class="p-daystep" href="{{ $dayNav['prev'] }}" rel="prev" aria-label="Previous day">&lsaquo;</a>
        <a class="p-daystep {{ $isToday ? 'is-on' : '' }}" href="{{ $dayNav['today'] }}">Today</a>
        <a class="p-daystep" href="{{ $dayNav['next'] }}" rel="next" aria-label="Next day">&rsaquo;</a>
    </div>
@endsection

@section('desktop_actions')
    <div class="d-stat" data-unassigned="{{ $unassigned }}">
        <div class="d-stat-value">{{ $unassigned }}</div>
        <div class="d-stat-label">Unassigned</div>
    </div>
    <div class="p-daynav">
        <a class="p-daystep" href="{{ $dayNav['prev'] }}" rel="prev" aria-label="Previous day">&lsaquo;</a>
        <a class="p-daystep {{ $isToday ? 'is-on' : '' }}" href="{{ $dayNav['today'] }}">Today</a>
        <a class="p-daystep" href="{{ $dayNav['next'] }}" rel="next" aria-label="Next day">&rsaquo;</a>
    </div>
@endsection

@section('content')

    {{-- A real date field, so a manager jumping three weeks back does not
         have to press an arrow twenty-one times. Submits on change; with no
         JavaScript it is still a form with a button. --}}
    <form class="p-daypick" method="GET" action="{{ $portalRoute('team') }}">
        <label class="p-label" for="team-date">Day</label>
        <input class="p-input" type="date" id="team-date" name="date"
               value="{{ $day->toDateString() }}" data-portal-dayjump>
        @if ($filter !== 'all')
            <input type="hidden" name="barber" value="{{ $filter }}">
        @endif
        <noscript><button class="p-btn p-btn-quiet" type="submit">Go</button></noscript>
    </form>

    {{-- The desk carries this as a stat in the header; the phone has no
         header room, so it reads as a line, exactly as Today does. --}}
    @if (! $portalIsDesktop)
        <div class="p-note" data-unassigned="{{ $unassigned }}">
            <strong>Unassigned: {{ $unassigned }}</strong><br>
            {{ $unassigned === 0
                ? 'Every booking that day has a barber.'
                : 'Listed below, under Unassigned.' }}
        </div>
    @endif

    @include('portal.partials.barber-filter')

    @forelse ($groups as $group)
        @include('portal.partials.barber-block', ['group' => $group])
    @empty
        <div class="p-empty">
            <div class="p-empty-title">Nothing booked</div>
            No appointments in the shop on {{ $day->format('j F') }}.
        </div>
    @endforelse
@endsection
