<?php

// tests/Feature/Http/Public/CheckoutControllerTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->has(Customer::factory())->create();
        $this->item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 100, 'quantity' => 1]);
        Country::factory()->create(['iso_code' => 'ES']);
    }

    private function addItemToCart(): void
    {
        $cart = [
            $this->item->id => ['quantity' => 1, 'name' => $this->item->name, 'price' => $this->item->sale_price],
        ];
        session(['cart' => $cart]);
    }

    #[Test]
    public function guests_are_redirected_from_checkout_to_login(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('login'));
    }

    #[Test]
    public function checkout_is_not_accessible_with_an_empty_cart(): void
    {
        $this->actingAs($this->user)->get(route('checkout.create'))
            ->assertRedirect(route('public.items.index'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function authenticated_users_with_items_can_see_the_checkout_page(): void
    {
        $this->addItemToCart();
        $this->actingAs($this->user)->get(route('checkout.create'))
            ->assertStatus(200)
            ->assertViewIs('public.checkout.index')
            ->assertSee($this->item->name);
    }

    #[Test]
    public function placing_an_order_with_new_address_requires_address_fields(): void
    {
        $this->addItemToCart();
        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'address_option' => 'new',
            'shipping_address' => ['recipient_name' => ''],
        ]);

        $response->assertSessionHasErrors('shipping_address.recipient_name');
        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function placing_an_order_with_existing_address_requires_a_valid_address_id(): void
    {
        $this->addItemToCart();
        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'address_option' => 'existing',
            'selected_address_id' => 999, // Non-existent address
        ]);

        $response->assertSessionHasErrors('selected_address_id');
        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function a_successful_order_can_be_placed_with_a_new_address(): void
    {
        $this->addItemToCart();
        $newAddressData = [
            'label' => 'New Address',
            'recipient_name' => 'Jane Doe',
            'street_address' => '456 New Ave',
            'city' => 'Newville',
            'postal_code' => '54321',
            'country_code' => 'ES',
            'state' => 'Seville',
            'phone' => '123456789',
        ];

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'address_option' => 'new',
            'shipping_address' => $newAddressData,
        ]);

        $this->assertEquals(1, Order::count());
        $order = Order::first();

        // THE FIX: Construct the expected redirect URL with the query parameter.
        $response->assertRedirect(route('checkout.success', ['orders' => $order->id]));

        $this->assertDatabaseHas('orders', ['user_id' => $this->user->id]);
        $this->assertDatabaseHas('addresses', array_merge($newAddressData, ['customer_id' => $this->user->customer->id]));
        $this->assertEmpty(session('cart'));
    }

    #[Test]
    public function a_successful_order_can_be_placed_with_an_existing_address(): void
    {
        $this->addItemToCart();
        $existingAddress = Address::factory()->create(['customer_id' => $this->user->customer->id]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'address_option' => 'existing',
            'selected_address_id' => $existingAddress->id,
        ]);

        $this->assertEquals(1, Order::count());
        $order = Order::first();

        // THE FIX: Construct the expected redirect URL with the query parameter.
        $response->assertRedirect(route('checkout.success', ['orders' => $order->id]));

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'address_id' => $existingAddress->id,
        ]);
        $this->assertEmpty(session('cart'));
    }
}
