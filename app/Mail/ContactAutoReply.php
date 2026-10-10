<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Support\Notify;
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
        // If the customer replies to the confirmation, it lands in the store inbox.
        $inbox = Notify::inbox();

        return new Envelope(
            replyTo: $inbox ? [new Address($inbox, settings('store_name', 'VanzaPack'))] : [],
            subject: 'We received your message — '.settings('store_name', 'VanzaPack'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact.auto-reply');
    }
}
