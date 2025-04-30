<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $type;

    public function __construct($data, $type = 'consultation')
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'consultation' 
            ? 'New Consultation Request' 
            : 'New Contact Message';

        return $this->subject($subject)
                    ->view('mail.admin_notification');
    }
}
