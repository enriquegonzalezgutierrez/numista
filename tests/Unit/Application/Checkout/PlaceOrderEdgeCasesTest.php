<?php

// tests/Unit/Application/Checkout/PlaceOrderEdgeCasesTest.php

namespace Tests\Unit\Application\Checkout;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Application\Checkout\PlaceOrderService;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlaceOrderEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Address $address;

    private PlaceOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->has(Customer::factory())->create();
        Country::factory()->create(['iso_code' => 'ES']);
        $this->address = Address::factory()->create(['customer_id' => $this->user->customer->id]);
        $this->service = app(PlaceOrderService::class);
    }

    /**
     * Data provider for item statuses that should prevent a purchase.
     */
    public static function invalidItemStatusProvider(): array
    {
        // THE FIX: Removed the 'null' case as it's an invalid database state.
        return [
            'item is in collection' => ['in_collection'],
            'item is already sold' => ['sold'],
            'item is featured but not for sale' => ['featured'],
        ];
    }

    #[Test]
    #[DataProvider('invalidItemStatusProvider')]
    public function it_throws_an_exception_if_an_item_in_the_cart_is_not_for_sale(string $status): void
    {
        // We expect an exception to be thrown, so the test should fail if it doesn't happen.
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item is not available for sale.');

        // Arrange: Create an item with a status that is not 'for_sale'
        $item = Item::factory()->create(['status' => $status, 'sale_price' => 100]);
        $cart = [$item->id => ['quantity' => 1]];
        $data = [
            'address_option' => 'existing',
            'selected_address_id' => $this->address->id,
            'shipping_address' => [],
        ];

        // Act: Attempt to place the order
        try {
            $this->service->handle($this->user, $cart, $data);
        } catch (\Exception $e) {
            // Assert: Ensure no order was created before re-throwing the exception
            $this->assertDatabaseCount('orders', 0);
            $this->assertDatabaseCount('order_items', 0);
            throw $e;
        }
    }

    #[Test]
    public function it_throws_an_exception_if_an_item_in_the_cart_has_insufficient_stock(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock for item.');

        // Arrange: Item has quantity 1, but user tries to buy 2.
        $item = Item::factory()->create(['status' => 'for_sale', 'sale_price' => 100, 'quantity' => 1]);
        $cart = [$item->id => ['quantity' => 2]];
        $data = [
            'address_option' => 'existing',
            'selected_address_id' => $this->address->id,
            'shipping_address' => [],
        ];

        // Act & Assert
        $this->service->handle($this->user, $cart, $data);
    }
}
