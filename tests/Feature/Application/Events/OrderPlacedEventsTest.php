<?php

// tests/Feature/Application/Events/OrderPlacedEventsTest.php

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
        // 1. Fake dependencies
        Event::fake();
        Mail::fake();

        // 2. Arrange the necessary data
        $user = User::factory()->has(Customer::factory())->create();

        // --- INICIO DE LA MODIFICACIÓN ---
        // Create an item with exactly one unit to test the status change to 'sold'
        $item = Item::factory()->create([
            'status' => 'for_sale',
            'sale_price' => 100,
            'quantity' => 1, // Only one item in stock
        ]);
        // --- FIN DE LA MODIFICACIÓN ---

        Country::factory()->create(['iso_code' => 'ES']);
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);
        $cart = [$item->id => ['quantity' => 1]];

        // 3. Act by calling the service.
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

        // 5. Manually run the listeners to check their side-effects
        $order->load('customer', 'items.item');

        (new \Numista\Collection\Application\Listeners\SendOrderConfirmationEmail)->handle(new OrderPlaced($order));
        (new \Numista\Collection\Application\Listeners\UpdateSoldItemStatus)->handle(new OrderPlaced($order));

        // 6. Assert the final state
        Mail::assertQueued(OrderConfirmationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Now, this assertion will be correct because the last item was sold.
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
            'quantity' => 0, // Also check that quantity was correctly decremented to zero
        ]);
    }
}
