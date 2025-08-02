<?php

// src/Collection/Application/Listeners/SendSubscriptionConfirmationEmail.php

namespace Numista\Collection\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Events\SubscriptionActivated;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionConfirmationMail;

class SendSubscriptionConfirmationEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SubscriptionActivated $event): void
    {
        $tenant = $event->tenant;
        $user = $tenant->users()->first(); // Get the primary user for the tenant

        if ($user) {
            Mail::to($user->email)->queue(new SubscriptionConfirmationMail($tenant, $user));
        }
    }
}
