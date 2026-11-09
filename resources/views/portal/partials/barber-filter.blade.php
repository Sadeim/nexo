{{-- Manager only: narrows Today and Bookings to one barber, or to the
     bookings nobody has been assigned yet.

     Links rather than a <select>, so the filter lives in the URL — it
     survives a refresh, it can be bookmarked, and it reads the same on a
     phone as at a desk. Built with $portalRoute and $portalScreen, so a chip
     always points at the screen you are on, in the version you are in. --}}
@php
    /*
     * Carries the Upcoming/Past tab and the sort direction across a filter
     * change. Without this, picking a barber while reading Past would quietly
     * drop the manager back onto Upcoming.
     */
    $keep = array_filter([
        'tab' => request()->query('tab') === 'past' ? 'past' : null,
        'dir' => in_array(request()->query('dir'), ['asc', 'desc'], true)
            ? request()->query('dir')
            : null,
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
