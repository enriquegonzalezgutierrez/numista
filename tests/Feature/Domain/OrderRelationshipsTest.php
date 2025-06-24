<?php

namespace Tests\Feature\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\OrderItem;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_order_can_have_multiple_items(): void
    {
        // Arrange: Create an order and add 3 items to it
        $order = Order::factory()
            ->has(OrderItem::factory()->count(3), 'items')
            ->create();

        // Assert: The order should have 3 items
        $this->assertCount(3, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items->first());
    }

    #[Test]
    public function an_order_belongs_to_a_customer_and_a_tenant(): void
    {
        // Arrange: Create an order
        $order = Order::factory()->create();

        // Assert: The relationships should be correctly set
        $this->assertNotNull($order->customer);
        $this->assertNotNull($order->tenant);
        $this->assertInstanceOf(\App\Models\User::class, $order->customer);
        $this->assertInstanceOf(\Numista\Collection\Domain\Models\Tenant::class, $order->tenant);
    }
}
