<?php

// src/Collection/Infrastructure/Mail/Orders/NewOrderNotificationMail.php

namespace Numista\Collection\Infrastructure\Mail\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Numista\Collection\Domain\Models\Order;

class NewOrderNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Order $order)
    {
        // Eager load necessary relationships for the email view
        $this->order->load('customer', 'items.item');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.seller_notification_subject', ['orderNumber' => $this->order->order_number]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.seller-notification', // We will create this view
        );
    }
}
