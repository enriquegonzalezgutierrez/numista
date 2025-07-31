<?php

// tests/Unit/Application/Checkout/PlaceOrderServiceTest.php

namespace Tests\Unit\Application\Checkout;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Numista\Collection\Application\Checkout\PlaceOrderService;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlaceOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Item $item;

    private array $cart;

    private Address $address;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->has(Customer::factory())->create();
        $this->item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 100, 'quantity' => 5]);
        $this->cart = [
            $this->item->id => ['quantity' => 2],
        ];
        Country::factory()->create(['iso_code' => 'ES']);
        $this->address = Address::factory()->create(['customer_id' => $this->user->customer->id]);
    }

    #[Test]
    public function it_creates_an_order_successfully(): void
    {
        Event::fake();
        $service = app(PlaceOrderService::class);
        $data = [
            'address_option' => 'existing',
            'selected_address_id' => $this->address->id,
            'shipping_address' => [],
        ];

        $orders = $service->handle($this->user, $this->cart, $data);

        // THE FIX: The service returns a collection, so we get the first order from it.
        $this->assertCount(1, $orders);
        $order = $orders->first();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'total_amount' => 200, // 100 (price) * 2 (quantity)
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'item_id' => $this->item->id,
            'quantity' => 2,
        ]);
    }

    #[Test]
    public function it_dispatches_the_order_placed_event_after_creating_an_order(): void
    {
        Event::fake();
        $service = app(PlaceOrderService::class);
        $data = [
            'address_option' => 'existing',
            'selected_address_id' => $this->address->id,
            'shipping_address' => [],
        ];

        $orders = $service->handle($this->user, $this->cart, $data);

        // THE FIX: The service dispatches one event per order. Since we only have one tenant,
        // it should dispatch the event once for the single order created.
        Event::assertDispatched(OrderPlaced::class, function ($event) use ($orders) {
            return $event->order->id === $orders->first()->id;
        });
    }

    #[Test]
    public function it_clears_the_cart_after_placing_an_order(): void
    {
        Event::fake();
        session(['cart' => $this->cart]);
        $this->assertNotEmpty(session('cart'));

        $service = app(PlaceOrderService::class);
        $data = [
            'address_option' => 'existing',
            'selected_address_id' => $this->address->id,
            'shipping_address' => [],
        ];

        $service->handle($this->user, $this->cart, $data);

        $this->assertEmpty(session('cart'));
    }
}
