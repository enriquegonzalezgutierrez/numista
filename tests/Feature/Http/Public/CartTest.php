<?php

// tests/Feature/Http/Public/CartTest.php

namespace Tests\Feature\Http\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_item_can_be_added_to_the_cart(): void
    {
        // Arrange
        $item = Item::factory()->create(['status' => 'for_sale']);

        // Act
        $response = $this->post(route('cart.add', $item));

        // Assert
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart', [
            $item->id => [
                'name' => $item->name,
                'quantity' => 1,
                'price' => $item->sale_price,
            ],
        ]);
    }

    #[Test]
    public function adding_the_same_item_increments_its_quantity(): void
    {
        // THE FIX: Ensure the item has enough stock for the test.
        $item = Item::factory()->create(['status' => 'for_sale', 'quantity' => 2]);

        // Add the item to the cart once
        $this->post(route('cart.add', $item));

        // Act: Add the same item again
        $response = $this->post(route('cart.add', $item));

        // Assert
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart.'.$item->id.'.quantity', 2);
    }

    #[Test]
    public function the_quantity_of_an_item_in_the_cart_can_be_updated(): void
    {
        // THE FIX: Create the item with enough stock to handle the update.
        $item = Item::factory()->create(['status' => 'for_sale', 'quantity' => 5]);
        $this->post(route('cart.add', $item)); // Initial quantity in cart is 1

        // Act: Update the quantity to 3
        $response = $this->patch(route('cart.update', $item), ['quantity' => 3]);

        // Assert
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart.'.$item->id.'.quantity', 3);
    }

    #[Test]
    public function an_item_can_be_removed_from_the_cart(): void
    {
        $item = Item::factory()->create(['status' => 'for_sale']);
        $this->post(route('cart.add', $item));

        $this->assertNotNull(session('cart.'.$item->id));

        // Act: Remove the item
        $response = $this->delete(route('cart.remove', $item));

        // Assert
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionMissing('cart.'.$item->id);
    }
}
