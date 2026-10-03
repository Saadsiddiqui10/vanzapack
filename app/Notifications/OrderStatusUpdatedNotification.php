<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order {$this->order->number} — {$this->order->status->label()}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your order {$this->order->number} is now: {$this->order->status->label()}.")
            ->action('View order', route('account.orders.show', $this->order->number))
            ->line('Thank you for shopping with '.config('store.name').'.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_status',
            'order_number' => $this->order->number,
            'status' => $this->order->status->value,
            'message' => "Order {$this->order->number} is now {$this->order->status->label()}.",
            'url' => route('account.orders.show', $this->order->number),
        ];
    }
}
