<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    /** Staff users see it in the admin panel; the email goes once to the store inbox (see Notify::staff). */
    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New order {$this->order->number}")
            ->line("A new order has been placed: {$this->order->number}")
            ->line('Total: '.money($this->order->grand_total))
            ->action('Open in admin', url('/admin/orders/'.$this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_order',
            'order_number' => $this->order->number,
            'total' => (float) $this->order->grand_total,
            'message' => "New order {$this->order->number} — ".money($this->order->grand_total),
            'url' => url('/admin/orders/'.$this->order->id),
        ];
    }
}
