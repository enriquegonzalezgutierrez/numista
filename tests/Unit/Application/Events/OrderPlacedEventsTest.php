<?php

// tests/Feature/Application/Events/OrderPlacedEventsTest.php

namespace Tests\Unit\Application\Events;

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
        Event::fake();
        Mail::fake();

        $user = User::factory()->has(Customer::factory())->create();
        $item = Item::factory()->create([
            'status' => 'for_sale',
            'sale_price' => 100, // THE FIX: Ensured sale_price is set
            'quantity' => 1,
        ]);

        Country::factory()->create(['iso_code' => 'ES']);
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);
        $cart = [$item->id => ['quantity' => 1]];

        $service = app(PlaceOrderService::class);
        $orders = $service->handle($user, $cart, [
            'address_option' => 'existing',
            'selected_address_id' => $address->id,
            'shipping_address' => [],
        ]);

        $this->assertCount(1, $orders);
        $order = $orders->first();

        Event::assertDispatched(OrderPlaced::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });

        $order->load('customer', 'items.item');
        (new \Numista\Collection\Application\Listeners\SendOrderConfirmationEmail)->handle(new OrderPlaced($order));
        (new \Numista\Collection\Application\Listeners\UpdateSoldItemStatus)->handle(new OrderPlaced($order));

        Mail::assertQueued(OrderConfirmationMail::class, fn ($mail) => $mail->hasTo($user->email));

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
            'quantity' => 0,
        ]);
    }
}
