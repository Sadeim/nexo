<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification
{
    use Queueable;

    protected $order;
    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تمت عملية الدفع بنجاح!')
            ->line('شكراً لشرائك معنا.')
            ->line('رقم الطلب: ' . $this->order->id)
            ->line('المبلغ المدفوع: ' . $this->order->amount . ' ' . $this->order->currency)
            ->action('عرض الطلب', url('/orders/' . $this->order->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
