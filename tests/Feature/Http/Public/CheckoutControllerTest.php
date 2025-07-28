<?php

// tests/Feature/Http/Public/CheckoutControllerTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 100]);
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
        $response = $this->get(route('checkout.create'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function checkout_is_not_accessible_with_an_empty_cart(): void
    {
        $response = $this->actingAs($this->user)->get(route('checkout.create'));
        $response->assertRedirect(route('public.items.index'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function authenticated_users_with_items_can_see_the_checkout_page(): void
    {
        $this->addItemToCart();
        $response = $this->actingAs($this->user)->get(route('checkout.create'));

        $response->assertStatus(200);
        $response->assertViewIs('public.checkout.index');
        $response->assertSee($this->item->name);
    }

    #[Test]
    public function placing_an_order_requires_a_shipping_address(): void
    {
        $this->addItemToCart();
        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'shipping_address' => '',
        ]);

        $response->assertSessionHasErrors('shipping_address');
        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function a_successful_order_can_be_placed(): void
    {
        $this->addItemToCart();
        $shippingAddress = '123 Test Street, Testville, 12345';

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'shipping_address' => $shippingAddress,
        ]);

        $this->assertEquals(1, Order::count());
        $order = Order::first();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_amount' => 100,
            'shipping_address' => $shippingAddress,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'item_id' => $this->item->id,
            'quantity' => 1,
            'price' => 100,
        ]);
        $this->assertEmpty(session('cart'));
    }
}
