<?php

// tests/Unit/Application/Listeners/UpdateSoldItemStatusTest.php

namespace Tests\Unit\Application\Listeners;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Application\Listeners\UpdateSoldItemStatus;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\OrderItem;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateSoldItemStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_the_item_status_to_sold_when_the_last_item_is_ordered(): void
    {
        // Arrange: Create an item for sale with exactly one unit.
        $item = Item::factory()->create([
            'status' => 'for_sale',
            'quantity' => 1,
        ]);

        // Arrange: Create an order that buys this last unit.
        $order = Order::factory()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1,
        ]);

        // Manually simulate the stock decrement that the PlaceOrderService would perform.
        $item->decrement('quantity', 1);

        $event = new OrderPlaced($order->load('items.item'));
        $listener = new UpdateSoldItemStatus;

        // Act: Manually trigger the listener's handle method.
        $listener->handle($event);

        // Assert: Check that the item's status has been updated in the database.
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
            'quantity' => 0,
        ]);
    }

    #[Test]
    public function it_does_not_change_status_if_stock_remains(): void
    {
        // Arrange: Create an item for sale with more than one unit.
        $item = Item::factory()->create([
            'status' => 'for_sale',
            'quantity' => 5, // Initially has 5.
        ]);

        // Simulate the stock decrement that the PlaceOrderService would do.
        $item->decrement('quantity', 2); // User buys 2.

        $order = Order::factory()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 2,
        ]);

        $event = new OrderPlaced($order->load('items.item'));
        $listener = new UpdateSoldItemStatus;

        // Act
        $listener->handle($event);

        // Assert: The status should remain 'for_sale' because there are items left.
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'for_sale',
            'quantity' => 3, // 5 - 2 = 3
        ]);
    }
}
