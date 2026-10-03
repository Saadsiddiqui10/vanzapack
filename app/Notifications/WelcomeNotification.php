<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to '.config('store.name'))
            ->greeting("Welcome, {$notifiable->name}!")
            ->line('Thanks for creating a '.config('store.name').' account.')
            ->line('You can now track orders, save a wishlist and check out faster.')
            ->action('Start shopping', route('shop.index'));
    }
}
