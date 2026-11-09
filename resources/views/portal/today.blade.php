@extends($portalLayout)
@section('title', 'Today')
@section('heading', 'Today')
@section('meta', $today->format('l j F'))

@section('actions')
    <button class="p-refresh" type="button" data-portal-refresh>Refresh</button>
@endsection

@section('desktop_actions')
    {{-- On the desk the unassigned count belongs in the header, beside the
         date, rather than as a banner over the timeline. --}}
    <div class="d-stat" data-unassigned="{{ $unassigned }}">
        <div class="d-stat-value">{{ $unassigned }}</div>
        <div class="d-stat-label">Unassigned today</div>
    </div>
    <button class="p-refresh" type="button" data-portal-refresh>Refresh</button>
@endsection

@section('content')
    @if ($portalIsManager)

        {{-- The whole shop's day, one block per barber in roster order, with
             the unassigned bookings named rather than counted: deciding who
             takes them is the manager's job, so they need to see them. --}}
        @include('portal.partials.barber-filter')

        @if (! $portalIsDesktop)
            <div class="p-note" data-unassigned="{{ $unassigned }}">
                <strong>Unassigned today: {{ $unassigned }}</strong><br>
                @if ($unassigned === 0)
                    Every booking today has a barber.
                @elseif (in_array($filter, ['all', 'unassigned'], true))
                    Listed below, under Unassigned.
                @else
                    Pick Unassigned above to see them.
                @endif
            </div>
        @endif

        @forelse ($groups as $group)
            @include('portal.partials.barber-block', ['group' => $group])
        @empty
            <div class="p-empty">
                <div class="p-empty-title">Nothing booked</div>
                No appointments in the shop today.
            </div>
        @endforelse

    @elseif ($portalIsDesktop)

        {{-- A timeline down the whole working day. The empty slots are the
             point as much as the full ones: a barber reading this wants to
             see the gaps. --}}
        <div class="d-timeline">
            @foreach ($timeline as $row)
                <div class="d-slot {{ $row['bookings']->isEmpty() ? 'is-free' : '' }}">
                    <div class="d-slot-time">{{ $row['label'] }}</div>
                    <div class="d-slot-body">
                        @forelse ($row['bookings'] as $booking)
                            @include('portal.partials.booking-card', ['booking' => $booking])
                        @empty
                            <div class="d-slot-free">Free</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        @if ($bookings->isEmpty())
            <div class="p-empty">
                <div class="p-empty-title">Nothing booked</div>
                You have no appointments today.
            </div>
        @endif

    @else

        <div class="p-note" data-unassigned="{{ $unassigned }}">
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

    @endif
@endsection
