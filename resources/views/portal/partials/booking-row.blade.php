{{-- One booking as a table row, for the desktop tables.

     $booking is required. $showDate adds the day (the Bookings table spans
     more than one), $showBarber adds whose it is (the manager's view). The
     two tables that use this must render the matching <th>s. --}}
@php
    // First name only: a barber needs to know who is in the chair, not to
    // carry the shop's customer list around.
    $firstName = trim(explode(' ', trim((string) $booking->name))[0] ?? '');
    $status = $booking->status ?: 'pending';
@endphp
<tr>
    <td class="d-cell-time">{{ \Carbon\Carbon::parse($booking->time)->format('g:i A') }}</td>
    @if (! empty($showDate))
        <td>{{ \Carbon\Carbon::parse($booking->date)->format('D j M Y') }}</td>
    @endif
    <td>{{ $firstName !== '' ? $firstName : 'Walk-in' }}</td>
    <td>{{ $booking->service->name ?? 'Service not set' }}</td>
    @if (! empty($showBarber))
        <td>{{ $booking->barber->name ?? 'Unassigned' }}</td>
    @endif
    <td><span class="p-badge p-badge-{{ $status }}">{{ $status }}</span></td>
</tr>
