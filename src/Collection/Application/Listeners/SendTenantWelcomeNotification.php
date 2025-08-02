<?php

// src/Collection/Application/Listeners/SendTenantWelcomeNotification.php

namespace Numista\Collection\Application\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Infrastructure\Mail\Auth\NewTenantWelcomeMail;

class SendTenantWelcomeNotification implements ShouldQueue
{
    /**
     * Handle the registered user event.
     */
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        // Only send the welcome email if the newly registered user is an admin/tenant.
        // This prevents customers who register via the normal process from getting this email.
        if ($user->is_admin) {
            Mail::to($user->email)->queue(new NewTenantWelcomeMail($user));
        }
    }
}
