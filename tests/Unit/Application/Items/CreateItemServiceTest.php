<?php

// tests/Unit/Application/Items/CreateItemServiceTest.php

namespace Tests\Unit\Application\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Application\Items\CreateItemService;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\SharedAttribute; // THE FIX: Use the new model
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateItemServiceTest extends TestCase
{
    use RefreshDatabase;

    private CreateItemService $service;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CreateItemService;
        $this->tenant = Tenant::factory()->create();
    }

    #[Test]
    public function it_creates_an_item_with_basic_data(): void
    {
        $data = [
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Coin',
            'type' => 'coin',
            'quantity' => 1,
            'status' => 'in_collection',
        ];

        $item = $this->service->handle($data);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Test Coin',
            'type' => 'coin',
        ]);
    }

    #[Test]
    public function it_creates_an_item_and_syncs_its_text_attributes(): void
    {
        // THE FIX: Use the new SharedAttribute model and factory
        $attribute = SharedAttribute::factory()->create(['type' => 'text']);

        $data = [
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Coin with Attributes',
            'type' => 'coin',
            'attributes' => [
                $attribute->id => ['value' => 'Silver'],
            ],
        ];

        $item = $this->service->handle($data);

        // THE FIX: Check the new pivot table 'item_attribute'
        $this->assertDatabaseHas('item_attribute', [
            'item_id' => $item->id,
            'shared_attribute_id' => $attribute->id,
            'value' => 'Silver',
        ]);
        $this->assertCount(1, $item->attributes);
    }

    #[Test]
    public function it_creates_an_item_and_syncs_its_select_attributes(): void
    {
        // THE FIX: Use the new models and factories
        $attribute = SharedAttribute::factory()->create(['type' => 'select']);
        $attributeValue = $attribute->options()->create(['value' => 'UNC']);

        $data = [
            'tenant_id' => $this->tenant->id,
            'name' => 'Graded Coin',
            'type' => 'coin',
            'attributes' => [
                $attribute->id => ['attribute_option_id' => $attributeValue->id],
            ],
        ];

        $item = $this->service->handle($data);

        // THE FIX: Check the new pivot table with the correct column names
        $this->assertDatabaseHas('item_attribute', [
            'item_id' => $item->id,
            'shared_attribute_id' => $attribute->id,
            'attribute_option_id' => $attributeValue->id,
            'value' => 'UNC',
        ]);
    }
}
