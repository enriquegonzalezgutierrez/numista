<?php

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
            // This assumes a quantity of 1 for simplicity.
            // A more complex system would handle stock reduction.
            $orderItem->item->update(['status' => 'sold']);
        }
    }
}
