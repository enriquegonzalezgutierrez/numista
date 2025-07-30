<?php

// tests/Unit/Application/Listeners/SendOrderConfirmationEmailTest.php

namespace Tests\Unit\Application\Listeners;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Application\Listeners\SendOrderConfirmationEmail;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Infrastructure\Mail\Orders\OrderConfirmationMail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendOrderConfirmationEmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_queues_an_order_confirmation_email(): void
    {
        // Arrange: Fake the Mail facade to capture outgoing emails.
        Mail::fake();

        // Arrange: Create an order with a customer.
        $order = Order::factory()->create();
        $order->load('customer'); // Eager load the customer relationship.

        $event = new OrderPlaced($order);
        $listener = new SendOrderConfirmationEmail;

        // Act: Manually trigger the listener's handle method.
        $listener->handle($event);

        // Assert: Check that a Mailable was queued for the correct user.
        Mail::assertQueued(OrderConfirmationMail::class, function ($mail) use ($order) {
            return $mail->hasTo($order->customer->email);
        });

        // Assert: Ensure the Mailable contains the correct order data.
        Mail::assertQueued(OrderConfirmationMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }
}
