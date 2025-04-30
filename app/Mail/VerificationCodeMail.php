<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // يحتوي على المستخدم ورمز OTP

    /**
     * إنشاء الكائن مع المستخدم.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * بناء الرسالة.
     */
    public function build()
    {
        return $this->subject('Your Verification Code')
                    ->view('mail.verification_code');
    }
}
