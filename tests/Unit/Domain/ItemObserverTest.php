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
        // Force slug to be null to ensure the observer is what generates it
        $item = Item::factory()->state(['slug' => null])->create([
            'name' => 'Moneda de Plata',
        ]);

        $this->assertNotNull($item->slug);
        $this->assertEquals('moneda-de-plata', $item->slug);
    }

    #[Test]
    public function it_generates_a_unique_slug_for_items_with_the_same_name(): void
    {
        // THE FIX: Explicitly set slug to null for both creations to ensure the Observer is tested.
        $item1 = Item::factory()->state(['slug' => null])->create(['name' => 'Moneda Repetida']);
        $item2 = Item::factory()->state(['slug' => null])->create(['name' => 'Moneda Repetida']);

        $this->assertEquals('moneda-repetida', $item1->slug);
        $this->assertEquals('moneda-repetida-1', $item2->slug);
    }
}
