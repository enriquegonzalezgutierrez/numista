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
        $item = Item::factory()->create([
            'name' => 'Moneda de Plata',
            'slug' => null, // Ensure slug is null initially
        ]);

        $this->assertNotNull($item->slug);
        $this->assertEquals('moneda-de-plata', $item->slug);
    }

    #[Test]
    public function it_generates_a_unique_slug_for_items_with_the_same_name(): void
    {
        Item::factory()->create(['name' => 'Moneda Repetida']);

        $newItem = Item::factory()->create(['name' => 'Moneda Repetida']);

        $this->assertNotNull($newItem->slug);
        $this->assertEquals('moneda-repetida-1', $newItem->slug);
    }
}