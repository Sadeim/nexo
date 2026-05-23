<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
  use Queueable, SerializesModels;

  public Booking $booking;

  public function __construct(Booking $booking)
  {
    $this->booking = $booking;
  }

  public function build()
  {
    return $this->subject(__('Reminder: Your Appointment in 30 Minutes - Nexo Barbers'))
      ->view('mail.booking_reminder');
  }
}
