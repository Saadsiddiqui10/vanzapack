<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Welcome email for new newsletter subscribers. */
class NewsletterWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to '.settings('store_name', 'VanzaPack').'!');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.newsletter.welcome');
    }
}
