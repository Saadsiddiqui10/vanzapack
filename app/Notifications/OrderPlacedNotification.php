<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Order confirmation — {$this->order->number}")
            ->greeting("Thank you, {$notifiable->name}!")
            ->line("We've received your order {$this->order->number}.");

        foreach ($this->order->items as $item) {
            $mail->line("• {$item->quantity} × {$item->name} — ".money($item->line_total));
        }

        return $mail
            ->line('**Total: '.money($this->order->grand_total).'**')
            ->action('View your order', route('account.orders.show', $this->order->number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_placed',
            'order_number' => $this->order->number,
            'total' => (float) $this->order->grand_total,
            'message' => "Order {$this->order->number} placed — ".money($this->order->grand_total),
            'url' => route('account.orders.show', $this->order->number),
        ];
    }
}
