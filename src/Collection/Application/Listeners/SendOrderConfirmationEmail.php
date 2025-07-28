<?php

namespace Numista\Collection\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Infrastructure\Mail\Orders\OrderConfirmationMail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->load('customer', 'items.item');

        Mail::to($order->customer->email)->send(
            new OrderConfirmationMail($order)
        );
    }
}
