<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Confirmation sent to the customer after they use the contact form. */
class ContactAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contact) {}

    public function envelope(): Envelope
    {
        $support = settings('store_support_email', config('store.support_email'));

        return new Envelope(
            replyTo: $support ? [new Address($support, settings('store_name', 'VanzaPack'))] : [],
            subject: 'We received your message — '.settings('store_name', 'VanzaPack'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact.auto-reply');
    }
}
