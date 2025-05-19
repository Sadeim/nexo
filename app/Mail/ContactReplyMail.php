<?php

namespace App\Mail;

use App\Models\UserMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $reply;

    public function __construct(UserMessages $contact, $reply)
    {
        $this->contact = $contact;
        $this->reply = $reply;
    }

    public function build()
    {
        return $this->subject('Reply to your inquiry')
                    ->view('mail.contact_reply')
                    ->with([
                        'name' => $this->contact->name,
                        'reply' => $this->reply,
                    ]);
    }
}
