<?php

namespace Tests\Unit\Application\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Application\Items\UpdateItemService;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateItemServiceTest extends TestCase
{
    use RefreshDatabase;

    private UpdateItemService $service;

    private Item $item;

    private Attribute $attribute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateItemService;
        $this->item = Item::factory()->create(['name' => 'Old Name']);
        $this->attribute = Attribute::factory()->create(['tenant_id' => $this->item->tenant_id]);
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
        $this->item->attributes()->attach($this->attribute->id, ['value' => 'Old Value']);

        $data = [
            'attributes' => [
                $this->attribute->id => ['value' => 'New Value'],
            ],
        ];

        $this->service->handle($this->item, $data);

        $this->assertDatabaseHas('item_attribute_value', [
            'item_id' => $this->item->id,
            'attribute_id' => $this->attribute->id,
            'value' => 'New Value',
        ]);
        $this->assertDatabaseMissing('item_attribute_value', [
            'item_id' => $this->item->id,
            'value' => 'Old Value',
        ]);
    }

    #[Test]
    public function it_removes_attributes_when_they_are_not_in_the_data_array(): void
    {
        $this->item->attributes()->attach($this->attribute->id, ['value' => 'Value To Be Removed']);

        // Data array without the 'attributes' key
        $data = ['name' => 'Name updated, attributes removed'];

        $this->service->handle($this->item, $data);

        $this->assertDatabaseMissing('item_attribute_value', [
            'item_id' => $this->item->id,
        ]);
        $this->assertCount(0, $this->item->fresh()->attributes);
    }
}
