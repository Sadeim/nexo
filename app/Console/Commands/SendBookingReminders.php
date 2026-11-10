<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send reminder emails 30 minutes before a booking, and refresh customers\' last visit once a day';

    /** The date of the last completed visit sweep. */
    private const SWEEP_KEY = 'customers.last_visit_swept_on';

    /**
     * Not before this hour, local time.
     *
     * This command runs every minute. The sweep is a pass over every booking
     * and every customer, so it runs once a day and it runs at the quietest
     * hour rather than during a haircut.
     */
    private const SWEEP_AFTER_HOUR = 3;

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

        $this->sweepVisitsIfDue();

        return self::SUCCESS;
    }

    /**
     * Bring every customer's last_visit_at up to date, once a day.
     *
     * It has to be a sweep rather than something that happens when an
     * appointment arrives: nothing happens then. The booking was written
     * weeks ago and nobody touches the row on the day. Without this, a
     * customer's last visit would only move when they next booked — and the
     * customer this column exists to find is the one who has not.
     *
     * Hung off this command because it is the one thing already running on a
     * schedule on this host. It is deliberately the last thing done here: a
     * failure in the sweep must never stop a reminder going out.
     */
    private function sweepVisitsIfDue(): void
    {
        try {
            $today = Carbon::now()->toDateString();

            if (Carbon::now()->hour < self::SWEEP_AFTER_HOUR) {
                return;
            }

            if (Cache::get(self::SWEEP_KEY) === $today) {
                return;
            }

            $changed = app(CustomerService::class)->refreshAllVisits();

            // Written after the work, so a sweep that dies half way is retried
            // on the next minute rather than skipped until tomorrow.
            Cache::forever(self::SWEEP_KEY, $today);

            $this->info("Refreshed last visit for {$changed} customer(s).");
        } catch (\Throwable $e) {
            // Including an unreachable cache, which would otherwise make this
            // run every minute instead of once.
            Log::error('Customer last-visit sweep failed: '.$e->getMessage());
            $this->error('Last-visit sweep failed: '.$e->getMessage());
        }
    }
}
