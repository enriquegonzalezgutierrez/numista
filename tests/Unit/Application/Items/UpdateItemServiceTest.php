<?php

// tests/Unit/Application/Items/UpdateItemServiceTest.php

namespace Tests\Unit\Application\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Application\Items\UpdateItemService;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\SharedAttribute; // THE FIX: Use the new model
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateItemServiceTest extends TestCase
{
    use RefreshDatabase;

    private UpdateItemService $service;

    private Item $item;

    private SharedAttribute $attribute; // THE FIX: Type hint the new model

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateItemService;
        $this->item = Item::factory()->create(['name' => 'Old Name']);

        // THE FIX: Use the new SharedAttribute factory and don't associate with a tenant
        $this->attribute = SharedAttribute::factory()->create();
    }

    #[Test]
    public function it_updates_an_items_basic_data(): void
    {
        $data = [
            'name' => 'New Updated Name',
            'status' => 'for_sale',
        ];

        $this->service->handle($this->item, $data);

        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'name' => 'New Updated Name',
            'status' => 'for_sale',
        ]);
    }

    #[Test]
    public function it_updates_an_items_attributes(): void
    {
        // THE FIX: Use the new pivot table and column name
        $this->item->attributes()->attach($this->attribute->id, ['value' => 'Old Value']);

        $data = [
            'attributes' => [
                $this->attribute->id => ['value' => 'New Value'],
            ],
        ];

        $this->service->handle($this->item, $data);

        // THE FIX: Assert against the new pivot table and column name
        $this->assertDatabaseHas('item_attribute', [
            'item_id' => $this->item->id,
            'shared_attribute_id' => $this->attribute->id,
            'value' => 'New Value',
        ]);
        $this->assertDatabaseMissing('item_attribute', [
            'item_id' => $this->item->id,
            'value' => 'Old Value',
        ]);
    }

    #[Test]
    public function it_removes_attributes_when_they_are_not_in_the_data_array(): void
    {
        $this->item->attributes()->attach($this->attribute->id, ['value' => 'Value To Be Removed']);

        // Data array without the 'attributes' key, but with an empty array to signify removal
        $data = [
            'name' => 'Name updated, attributes removed',
            'attributes' => [], // Explicitly sending an empty array is the correct way to signal a sync(null)
        ];

        $this->service->handle($this->item, $data);

        // THE FIX: Check the new pivot table
        $this->assertDatabaseMissing('item_attribute', [
            'item_id' => $this->item->id,
        ]);
        $this->assertCount(0, $this->item->fresh()->attributes);
    }
}
