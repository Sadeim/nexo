{{-- One barber's share of the day, for a manager's Today.

     Cards on a phone, a compact table at a desk: one file branching on the
     layout, because it is the same block of information either way. --}}
<section class="p-group" data-barber="{{ $group['key'] }}">
    <header class="p-group-head">
        <h2 class="p-group-name">{{ $group['label'] }}</h2>
        <span class="p-group-count">
            {{ $group['bookings']->count() }} {{ Str::plural('booking', $group['bookings']->count()) }}
        </span>
    </header>

    @if ($group['bookings']->isEmpty())

        {{-- Only reachable with a chip selected: on All, an empty block is
             left out rather than shown. --}}
        <div class="p-group-empty">Nothing today.</div>

    @elseif ($portalIsDesktop)

        <table class="d-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group['bookings'] as $booking)
                    @include('portal.partials.booking-row', ['booking' => $booking, 'showDate' => false, 'showBarber' => false])
                @endforeach
            </tbody>
        </table>

    @else

        @foreach ($group['bookings'] as $booking)
            @include('portal.partials.booking-card', ['booking' => $booking, 'showDate' => false, 'showBarber' => false])
        @endforeach

    @endif
</section>
