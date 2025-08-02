<?php

// src/Collection/Application/Listeners/UpdateSoldItemStatus.php

namespace Numista\Collection\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Numista\Collection\Domain\Events\OrderPlaced;

class UpdateSoldItemStatus implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        foreach ($event->order->items as $orderItem) {
            $item = $orderItem->item;

            // Only change status to 'sold' if the quantity is zero after the purchase.
            if ($item->quantity <= 0) {
                $item->update(['status' => 'sold']);
            }
        }
    }
}
