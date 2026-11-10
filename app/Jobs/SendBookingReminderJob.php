<?php

namespace App\Jobs;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingReminderJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public Booking $booking;

  public function __construct(Booking $booking)
  {
    $this->booking = $booking;
  }

  public function handle(): void
  {
    // تأكد أن الحجز لا يزال pending أو confirmed قبل الإرسال
    $this->booking->refresh();

    if (!in_array($this->booking->status, ['pending', 'confirmed'])) {
      return;
    }

    // An email address is optional on a booking since 10 Nov 2026 — the
    // phone number is what the shop asks for now. Mail::to(null) throws,
    // and a reminder nobody can receive is not worth a failed job.
    if (blank($this->booking->email)) {
      return;
    }

    try {
      Mail::to($this->booking->email)->send(new BookingReminderMail($this->booking));
    } catch (\Throwable $e) {
      \Log::warning('Booking reminder email failed: ' . $e->getMessage());
    }
  }
}
