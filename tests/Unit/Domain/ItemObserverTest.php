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
        // Arrange: Create an item with a specific name, ensuring slug is null initially.
        $item = Item::factory()->state(['slug' => null])->create([
            'name' => 'Moneda de Plata',
        ]);

        // Assert: The observer correctly generated the slug.
        $this->assertEquals('moneda-de-plata', $item->slug);
    }

    #[Test]
    public function it_generates_a_unique_slug_for_items_with_the_same_name(): void
    {
        // Arrange: Create two items with the exact same name.
        $item1 = Item::factory()->state(['slug' => null])->create(['name' => 'Moneda Repetida']);
        $item2 = Item::factory()->state(['slug' => null])->create(['name' => 'Moneda Repetida']);
        $item3 = Item::factory()->state(['slug' => null])->create(['name' => 'Moneda Repetida']);

        // Assert: The observer correctly appends suffixes to maintain uniqueness.
        $this->assertEquals('moneda-repetida', $item1->slug);
        $this->assertEquals('moneda-repetida-1', $item2->slug);
        $this->assertEquals('moneda-repetida-2', $item3->slug);
    }

    #[Test]
    public function it_updates_the_slug_when_the_item_name_is_changed(): void
    {
        // Arrange: Create an item.
        $item = Item::factory()->create(['name' => 'Old Name']);
        $this->assertEquals('old-name', $item->slug);

        // Act: Update the name of the item.
        $item->name = 'New Updated Name';
        $item->save();

        // Assert: The observer detected the name change and updated the slug.
        $this->assertEquals('new-updated-name', $item->fresh()->slug);
    }

    #[Test]
    public function it_does_not_update_the_slug_if_the_name_is_not_changed(): void
    {
        // Arrange: Create an item.
        $item = Item::factory()->create(['name' => 'Original Name']);
        $originalSlug = $item->slug;

        // Act: Update a different attribute of the item.
        $item->status = 'for_sale';
        $item->save();

        // Assert: The slug remains unchanged because the name was not modified.
        $this->assertEquals($originalSlug, $item->fresh()->slug);
    }
}
