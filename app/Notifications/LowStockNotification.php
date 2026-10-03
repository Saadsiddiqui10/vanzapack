<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public Inventory $inventory) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->inventory->product?->name ?? 'Product';

        return (new MailMessage)
            ->subject("Low stock: {$name}")
            ->line("{$name} is low on stock ({$this->inventory->available()} left).")
            ->action('Manage inventory', url('/admin/inventory'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_stock',
            'product' => $this->inventory->product?->name,
            'available' => $this->inventory->available(),
            'message' => ($this->inventory->product?->name ?? 'Product').' is low on stock.',
            'url' => url('/admin/inventory'),
        ];
    }
}
