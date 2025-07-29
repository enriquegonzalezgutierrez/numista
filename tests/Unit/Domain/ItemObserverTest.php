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
        // THE FIX: Create the item with a specific name. The observer will handle the slug.
        $item = Item::factory()->create([
            'name' => 'Moneda de Plata',
        ]);

        $this->assertNotNull($item->slug);
        $this->assertEquals('moneda-de-plata', $item->slug);
    }

    #[Test]
    public function it_generates_a_unique_slug_for_items_with_the_same_name(): void
    {
        // This test logic is now correct as it relies on the observer.
        $item1 = Item::factory()->create(['name' => 'Moneda Repetida']);
        $item2 = Item::factory()->create(['name' => 'Moneda Repetida']);

        $this->assertEquals('moneda-repetida', $item1->slug);
        $this->assertEquals('moneda-repetida-1', $item2->slug);
    }
}
