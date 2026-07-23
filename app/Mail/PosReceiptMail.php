<?php

namespace App\Mail;

use App\Models\PosOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PosReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public PosOrder $order;

    public function __construct(PosOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Your receipt from Nexo Barbers — #' . $this->order->order_number)
            ->view('mail.pos_receipt');
    }
}
