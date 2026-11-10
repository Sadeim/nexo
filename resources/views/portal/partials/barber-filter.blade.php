{{-- Manager only: narrows Today and Bookings to one barber, or to the
     bookings nobody has been assigned yet.

     Links rather than a <select>, so the filter lives in the URL — it
     survives a refresh, it can be bookmarked, and it reads the same on a
     phone as at a desk. Built with $portalRoute and $portalScreen, so a chip
     always points at the screen you are on, in the version you are in. --}}
@php
    /*
     * Carries the day across a filter change. Picking a barber while looking
     * at Thursday must not silently drop the manager back onto today — the
     * chips answer "whose", not "when".
     *
     * Taken from the controller's resolved day rather than the raw query, so
     * a nonsense ?date= cannot be propagated through every chip.
     */
    $keep = array_filter([
        'date' => isset($day) ? $day->toDateString() : null,
    ]);
@endphp

<div class="p-chips" data-filter="{{ $filter }}">
    <a class="p-chip {{ $filter === 'all' ? 'p-chip-on' : '' }}"
       href="{{ $portalRoute($portalScreen, $keep) }}">All</a>

    @foreach ($barbers as $barber)
        <a class="p-chip {{ $filter === (string) $barber->id ? 'p-chip-on' : '' }}"
           href="{{ $portalRoute($portalScreen, $keep + ['barber' => $barber->id]) }}">{{ $barber->first_name }}</a>
    @endforeach

    <a class="p-chip {{ $filter === 'unassigned' ? 'p-chip-on' : '' }}"
       href="{{ $portalRoute($portalScreen, $keep + ['barber' => 'unassigned']) }}">Unassigned</a>
</div>
