<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send reminder emails to customers 30 minutes before their booking time';

    public function handle(): int
    {
        $now = Carbon::now();
        $windowEnd = $now->copy()->addMinutes(30);

        $bookings = Booking::with('service')
            ->whereNull('reminder_sent_at')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('email')
            ->whereBetween('date', [$now->toDateString(), $windowEnd->toDateString()])
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            $appointment = Carbon::parse($booking->date->format('Y-m-d') . ' ' . $booking->time);

            if ($appointment->lessThanOrEqualTo($now) || $appointment->greaterThan($windowEnd)) {
                continue;
            }

            try {
                Mail::to($booking->email)->send(new BookingReminderMail($booking));
                $booking->forceFill(['reminder_sent_at' => now()])->save();
                $sent++;
            } catch (\Throwable $e) {
                Log::error("Booking reminder failed for #{$booking->id}: {$e->getMessage()}");
                $this->error("Failed for booking #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$sent} reminder(s).");

        return self::SUCCESS;
    }
}
