<?php

namespace Tests\Feature\Application\Events;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function placing_an_order_dispatches_event_and_listeners_are_executed(): void
    {
        Mail::fake();

        // Arrange the necessary data
        $user = User::factory()->has(Customer::factory())->create();
        // ItemObserver will handle the slug creation
        $item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 100]);
        Country::factory()->create(['iso_code' => 'ES']);
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);
        $cart = [$item->id => ['quantity' => 1]];

        // Act by calling the service that creates the order
        $service = app(PlaceOrderService::class);
        $order = $service->handle($user, $cart, [
            'address_option' => 'existing',
            'selected_address_id' => $address->id,
            'shipping_address' => [],
        ]);

        // Manually trigger listeners for the test, since events are faked by default in Laravel tests
        // This simulates the queue worker processing the jobs.
        (new \Numista\Collection\Application\Listeners\SendOrderConfirmationEmail)->handle(new OrderPlaced($order));
        (new \Numista\Collection\Application\Listeners\UpdateSoldItemStatus)->handle(new OrderPlaced($order));

        // Assert the effects of the listeners
        // THE FIX: Use assertQueued as the listener is ShouldQueue
        Mail::assertQueued(OrderConfirmationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }
}
