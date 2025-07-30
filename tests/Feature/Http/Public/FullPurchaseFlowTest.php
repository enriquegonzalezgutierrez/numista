<?php

// tests/Feature/Http/FullPurchaseFlowTest.php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FullPurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_registered_user_can_complete_a_purchase_with_a_new_address(): void
    {
        // 1. Arrange: Prepare the environment
        Event::fake();
        $user = User::factory()->has(Customer::factory())->create();
        $item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 120.50]);
        Country::factory()->create(['iso_code' => 'ES']);

        $newAddressData = [
            'label' => 'Casa Principal',
            'recipient_name' => $user->name,
            'street_address' => '123 Calle Falsa',
            'city' => 'Sevilla',
            'postal_code' => '41001',
            'country_code' => 'ES',
        ];

        // 2. Act: Simulate the user's journey
        $this
            ->actingAs($user)
            // Step 1: Add item to cart
            ->withSession(['cart' => [$item->id => ['quantity' => 1]]])
            // Step 2: Go to checkout page
            ->get(route('checkout.create'))
            ->assertOk();

        // Step 3: Submit the checkout form
        $response = $this->post(route('checkout.store'), [
            'address_option' => 'new',
            'shipping_address' => $newAddressData,
        ]);

        // Refresh the user model from the database to load the newly created address
        $user->refresh();

        // 3. Assert: Check the results of the entire flow
        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();

        $response->assertRedirect(route('checkout.success', $order));

        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(120.50, $order->total_amount);
        $this->assertCount(1, $order->items);

        $this->assertDatabaseHas('addresses', array_merge($newAddressData, ['customer_id' => $user->customer->id]));

        // Now this assertion will work because the user model has been refreshed
        $this->assertEquals($user->customer->addresses->first()->id, $order->address_id);

        $this->assertEmpty(session('cart'));

        Event::assertDispatched(OrderPlaced::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });

        $this
            ->get(route('my-account.orders'))
            ->assertOk()
            ->assertSee($order->order_number);
    }
}
