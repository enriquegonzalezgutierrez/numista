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
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FullPurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_registered_user_can_complete_a_purchase_with_a_new_address(): void
    {
        Event::fake();
        $user = User::factory()->has(Customer::factory())->create();
        $item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 120.50, 'quantity' => 1]);
        Country::factory()->create(['iso_code' => 'ES']);
        $newAddressData = [
            'label' => 'Casa Principal',
            'recipient_name' => $user->name,
            'street_address' => '123 Calle Falsa',
            'city' => 'Sevilla',
            'postal_code' => '41001',
            'country_code' => 'ES',
        ];

        $this->actingAs($user)->withSession(['cart' => [$item->id => ['quantity' => 1]]])
            ->get(route('checkout.create'))->assertOk();

        $response = $this->post(route('checkout.store'), [
            'address_option' => 'new',
            'shipping_address' => $newAddressData,
        ]);

        $user->refresh();
        $order = Order::first();

        $response->assertRedirect(route('checkout.success', ['orders' => $order->id]));
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $this->assertDatabaseHas('addresses', array_merge($newAddressData, ['customer_id' => $user->customer->id]));
        $this->assertEmpty(session('cart'));
        Event::assertDispatched(OrderPlaced::class);
    }

    #[Test]
    public function it_creates_separate_orders_for_items_from_different_tenants(): void
    {
        $this->withoutExceptionHandling();
        Event::fake();
        $user = User::factory()->has(Customer::factory())->create();
        Country::factory()->create(['iso_code' => 'ES']);
        $address = $user->customer->addresses()->create(\Numista\Collection\Domain\Models\Address::factory()->raw());

        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        // THE FIX: Ensure items have a sale_price.
        $item1 = Item::factory()->create(['tenant_id' => $tenant1->id, 'status' => 'for_sale', 'quantity' => 1, 'sale_price' => 50]);
        $item2 = Item::factory()->create(['tenant_id' => $tenant2->id, 'status' => 'for_sale', 'quantity' => 1, 'sale_price' => 75]);

        $cart = [
            $item1->id => ['quantity' => 1],
            $item2->id => ['quantity' => 1],
        ];

        $this->actingAs($user)->withSession(['cart' => $cart])->post(route('checkout.store'), [
            'address_option' => 'existing',
            'selected_address_id' => $address->id,
        ]);

        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseHas('orders', ['tenant_id' => $tenant1->id, 'total_amount' => 50]);
        $this->assertDatabaseHas('orders', ['tenant_id' => $tenant2->id, 'total_amount' => 75]);
        Event::assertDispatched(OrderPlaced::class, 2);
    }

    #[Test]
    public function adding_an_item_to_cart_with_insufficient_stock_fails(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'for_sale', 'quantity' => 1]);

        $this->actingAs($user)->post(route('cart.add.async', $item))->assertOk();
        $response = $this->actingAs($user)->post(route('cart.add.async', $item));

        $response->assertStatus(409);
        $response->assertJson(['success' => false]);
        $this->assertEquals(1, session('cart')[$item->id]['quantity']);
    }

    #[Test]
    public function checkout_fails_if_stock_changes_after_item_was_added_to_cart(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->has(Customer::factory())->create();
        $address = $user->customer->addresses()->create(\Numista\Collection\Domain\Models\Address::factory()->raw());
        $item = Item::factory()->create(['status' => 'for_sale', 'quantity' => 1]);

        $cart = [$item->id => ['quantity' => 1]];
        $item->update(['quantity' => 0]);

        try {
            $this->actingAs($user)->withSession(['cart' => $cart])->post(route('checkout.store'), [
                'address_option' => 'existing',
                'selected_address_id' => $address->id,
            ]);
        } catch (\Exception $e) {
            $this->assertStringContainsString($item->name, $e->getMessage());
            $this->assertStringContainsString('ya no está disponible', $e->getMessage());
            $this->assertDatabaseCount('orders', 0);

            return;
        }

        $this->fail('An exception was expected but not thrown.');
    }
}
