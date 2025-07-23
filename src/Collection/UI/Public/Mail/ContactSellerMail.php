<?php

// src/Collection/UI/Public/Mail/ContactSellerMail.php

namespace Numista\Collection\UI\Public\Mail;

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
            replyTo: $this->fromEmail,
            // Use the translation key for the subject
            subject: __('mail.contact_subject', ['itemName' => $this->item->name]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // This points to the Blade view for the email content
        return new Content(
            markdown: 'emails.contact.seller',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
