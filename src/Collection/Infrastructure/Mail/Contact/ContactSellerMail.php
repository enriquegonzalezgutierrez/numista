<?php

// src/Collection/Infrastructure/Mail/Contact/ContactSellerMail.php

namespace Numista\Collection\Infrastructure\Mail\Contact;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Numista\Collection\Domain\Models\Item;

class ContactSellerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  \Numista\Collection\Domain\Models\Item  $item  The item being inquired about.
     * @param  string  $fromName  The name of the person sending the message.
     * @param  string  $fromEmail  The email of the person sending the message.
     * @param  string  $body  The message content.
     */
    public function __construct(
        public Item $item,
        public string $fromName,
        public string $fromEmail,
        public string $body
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            replyTo: $this->fromEmail, // Allows the seller to reply directly to the buyer's email.
            subject: __('mail.contact_subject', ['itemName' => $this->item->name]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.seller',
        );
    }
}
