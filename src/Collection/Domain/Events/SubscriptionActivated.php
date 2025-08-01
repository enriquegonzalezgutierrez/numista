<?php

// src/Collection/Domain/Events/SubscriptionActivated.php

namespace Numista\Collection\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Numista\Collection\Domain\Models\Tenant;

class SubscriptionActivated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Tenant $tenant) {}
}
