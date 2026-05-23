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

    try {
      Mail::to($this->booking->email)->send(new BookingReminderMail($this->booking));
    } catch (\Throwable $e) {
      \Log::warning('Booking reminder email failed: ' . $e->getMessage());
    }
  }
}
