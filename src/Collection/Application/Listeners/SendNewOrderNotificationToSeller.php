<?php

// src/Collection/Application/Listeners/SendNewOrderNotificationToSeller.php

namespace Numista\Collection\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Infrastructure\Mail\Orders\NewOrderNotificationMail; // We will create this

class SendNewOrderNotificationToSeller implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        // An order belongs to a tenant, which has admin users (sellers).
        $seller = $order->tenant->users()->first();

        if (! $seller || ! $seller->email) {
            Log::error("Could not find a seller email for tenant ID: {$order->tenant_id} to notify about new order ID: {$order->id}");

            return;
        }

        // Queue the email to the seller.
        Mail::to($seller->email)->queue(
            new NewOrderNotificationMail($order)
        );
    }
}
