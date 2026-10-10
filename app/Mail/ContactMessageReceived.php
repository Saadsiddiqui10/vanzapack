<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the store inbox when someone submits the contact form. Reply goes straight to the customer. */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contact) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contact->email, $this->contact->name)],
            subject: 'Website enquiry: '.($this->contact->subject ?: 'New message from '.$this->contact->name),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact.received');
    }
}
