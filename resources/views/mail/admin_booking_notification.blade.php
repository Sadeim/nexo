<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('New Booking') }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #8C1C13;">{{ __('New Booking Received') }}</h2>
        <p>{{ __('A new appointment has been booked with the following details:') }}</p>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>{{ __('Name') }}:</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $booking->name }}</td></tr>
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>{{ __('Email') }}:</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $booking->email }}</td></tr>
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>{{ __('Date') }}:</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $booking->date->format('l, F j, Y') }}</td></tr>
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>{{ __('Time') }}:</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ \Carbon\Carbon::parse($booking->time)->format('g:i A') }}</td></tr>
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>{{ __('Service') }}:</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $booking->service?->name ?? '-' }}</td></tr>
        </table>
        <p>{{ __('Please check your admin panel for more options.') }}</p>
    </div>
</body>
</html>
