{{-- One booking, as it appears on both Today and Bookings.
     $booking is required; $showDate adds the day for the Bookings list. --}}
@php
    $status = $booking->status ?: 'pending';
    // Only the customer's first name. A barber needs to know who is in the
    // chair, not to carry the shop's customer list around on their phone.
    $firstName = trim(explode(' ', trim((string) $booking->name))[0] ?? '');
@endphp

<article class="p-card">
    <div class="p-card-row">
        <div>
            <div class="p-name">{{ $firstName !== '' ? $firstName : 'Walk-in' }}</div>
            <div class="p-service">{{ $booking->service->name ?? 'Service not set' }}</div>
            @if (! empty($showDate))
                <div class="p-date">{{ \Carbon\Carbon::parse($booking->date)->format('D j M Y') }}</div>
            @endif
        </div>
        <div style="text-align:right">
            <div class="p-time">{{ \Carbon\Carbon::parse($booking->time)->format('g:i A') }}</div>
            <div style="margin-top:6px">
                <span class="p-badge p-badge-{{ $status }}">{{ $status }}</span>
            </div>
        </div>
    </div>
</article>
