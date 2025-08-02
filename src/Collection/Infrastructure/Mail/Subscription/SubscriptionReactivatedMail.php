<?php

// src/Collection/Infrastructure/Mail/Subscription/SubscriptionReactivatedMail.php

namespace Numista\Collection\Infrastructure\Mail\Subscription;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Numista\Collection\Domain\Models\Tenant;

class SubscriptionReactivatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Tenant $tenant, public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.subscription_reactivated_subject'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.subscription.reactivated');
    }
}
