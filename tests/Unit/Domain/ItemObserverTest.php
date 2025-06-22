<?php

// tests/Unit/Domain/ItemObserverTest.php

namespace Tests\Unit\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemObserverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_automatically_generates_a_slug_when_creating_an_item(): void
    {
        // 1. Arrange: Prepare the data
        $item = Item::factory()->create([
            'name' => 'Moneda de Plata',
            'slug' => null, // Ensure slug is null initially
        ]);

        // 2. Act: The action happens in the 'create()' method above, triggering the observer.

        // 3. Assert: Verify the result
        $this->assertNotNull($item->slug);
        $this->assertEquals('moneda-de-plata', $item->slug);
    }

    #[Test]
    public function it_generates_a_unique_slug_for_items_with_the_same_name(): void
    {
        // 1. Arrange: Create the first item
        Item::factory()->create(['name' => 'Moneda Repetida']);

        // 2. Act: Create a second item with the exact same name
        $newItem = Item::factory()->create(['name' => 'Moneda Repetida']);

        // 3. Assert: Verify the new slug is unique
        $this->assertNotNull($newItem->slug);
        $this->assertEquals('moneda-repetida-1', $newItem->slug);
    }
}
