<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Tells the store inbox that someone joined the newsletter. */
class NewsletterSubscribed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New newsletter subscriber: '.$this->subscriber->email);
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.newsletter.subscribed', with: [
            'total' => NewsletterSubscriber::query()->whereNull('unsubscribed_at')->count(),
        ]);
    }
}
