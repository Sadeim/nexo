<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminBookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject(__('New Booking - :date at :time', [
            'date' => $this->booking->date->format('Y-m-d'),
            'time' => \Carbon\Carbon::parse($this->booking->time)->format('H:i'),
        ]))
            ->view('mail.admin_booking_notification');
    }
}
