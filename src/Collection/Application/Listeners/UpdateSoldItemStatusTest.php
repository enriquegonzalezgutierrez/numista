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
    public function it_updates_the_item_status_to_sold_after_an_order_is_placed(): void
    {
        // Arrange: Create an item that is for sale with a specific quantity.
        $item = Item::factory()->create([
            'status' => 'for_sale',
            'quantity' => 5,
        ]);

        // Arrange: Create an order that includes this item.
        $order = Order::factory()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1, // The user buys 1 unit.
        ]);

        // The 'handle' method in PlaceOrderService already decrements the quantity,
        // so we are just testing the status change here.
        // If stock logic were moved *only* to a listener, we would test that here too.

        $event = new OrderPlaced($order->load('items.item'));
        $listener = new UpdateSoldItemStatus;

        // Act: Manually trigger the listener's handle method.
        $listener->handle($event);

        // Assert: Check that the item's status has been updated in the database.
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
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

        // The order service would have already decremented the quantity.
        // Let's simulate that here.
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

    #[Test]
    public function it_changes_status_to_sold_only_when_last_item_is_sold(): void
    {
        // Arrange: Item has only 1 unit left.
        $item = Item::factory()->create([
            'status' => 'for_sale',
            'quantity' => 1,
        ]);

        // The order service decrements the quantity to 0.
        $item->decrement('quantity', 1);

        $order = Order::factory()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1,
        ]);

        $event = new OrderPlaced($order->load('items.item'));
        $listener = new UpdateSoldItemStatus;

        // Act
        $listener->handle($event);

        // Assert: Now the status should change to 'sold'.
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
            'quantity' => 0,
        ]);
    }
}
