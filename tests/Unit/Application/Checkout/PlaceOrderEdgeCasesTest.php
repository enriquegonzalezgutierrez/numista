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

    public static function invalidItemStatusProvider(): array
    {
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
        $this->expectException(\Exception::class);
        // THE FIX: Expect the translated error message from the service.
        $this->expectExceptionMessage(__('public.checkout_error_item_not_available', ['itemName' => 'Test Item']));

        $item = Item::factory()->create(['name' => 'Test Item', 'status' => $status, 'sale_price' => 100]);
        $cart = [$item->id => ['quantity' => 1]];
        $data = [
            'address_option' => 'existing',
            'selected_address_id' => $this->address->id,
            'shipping_address' => [],
        ];

        try {
            $this->service->handle($this->user, $cart, $data);
        } catch (\Exception $e) {
            $this->assertDatabaseCount('orders', 0);
            // Re-throw the exception for PHPUnit to catch and validate the message.
            throw $e;
        }
    }

    #[Test]
    public function it_throws_an_exception_if_an_item_in_the_cart_has_insufficient_stock(): void
    {
        $this->expectException(\Exception::class);
        // THE FIX: Expect the translated error message.
        $this->expectExceptionMessage(__('public.checkout_error_item_not_available', ['itemName' => 'Test Item']));

        $item = Item::factory()->create(['name' => 'Test Item', 'status' => 'for_sale', 'sale_price' => 100, 'quantity' => 1]);
        $cart = [$item->id => ['quantity' => 2]]; // Requesting 2, only 1 available
        $data = [
            'address_option' => 'existing',
            'selected_address_id' => $this->address->id,
            'shipping_address' => [],
        ];

        $this->service->handle($this->user, $cart, $data);
    }
}
