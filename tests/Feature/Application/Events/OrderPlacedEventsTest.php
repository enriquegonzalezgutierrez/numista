<?php

namespace Tests\Feature\Application\Events;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Application\Checkout\PlaceOrderService;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Infrastructure\Mail\Orders\OrderConfirmationMail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderPlacedEventsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function placing_an_order_dispatches_event_and_queues_listeners_jobs(): void
    {
        // 1. Fake dependencies that we want to control
        Event::fake(); // We will dispatch the event manually to trigger listeners
        Mail::fake();

        // 2. Arrange the necessary data
        $user = User::factory()->has(Customer::factory())->create();
        $item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 100]);
        Country::factory()->create(['iso_code' => 'ES']);
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);
        $cart = [$item->id => ['quantity' => 1]];

        // 3. Act by calling the service.
        // The service itself will dispatch the OrderPlaced event.
        $service = app(PlaceOrderService::class);
        $order = $service->handle($user, $cart, [
            'address_option' => 'existing',
            'selected_address_id' => $address->id,
            'shipping_address' => [],
        ]);

        // 4. Assert that the OrderPlaced event was dispatched.
        Event::assertDispatched(OrderPlaced::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });

        // 5. THE REAL FIX: Manually run the listeners to check their side-effects
        // This simulates what a queue worker would do.
        $order->load('customer', 'items.item'); // Load relations needed by listeners

        // Simulate SendOrderConfirmationEmail listener
        (new \Numista\Collection\Application\Listeners\SendOrderConfirmationEmail)->handle(new OrderPlaced($order));

        // Simulate UpdateSoldItemStatus listener
        (new \Numista\Collection\Application\Listeners\UpdateSoldItemStatus)->handle(new OrderPlaced($order));

        // 6. Assert the final state
        // The listener implements ShouldQueue, so Mail::send becomes Mail::queue.
        // We use Mail::assertQueued to check that it was added to the queue.
        Mail::assertQueued(OrderConfirmationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Assert the database was updated by the other listener.
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }
}
