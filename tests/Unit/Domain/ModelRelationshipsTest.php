<?php

// tests/Unit/Domain/ModelRelationshipsTest.php

namespace Tests\Unit\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_item_can_have_categories_attached(): void
    {
        $item = Item::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $item->categories()->attach([$category1->id, $category2->id]);

        $this->assertCount(2, $item->fresh()->categories);
        $this->assertTrue($item->categories->contains($category1));
    }

    #[Test]
    public function an_item_can_have_images(): void
    {
        $item = Item::factory()->create();

        $item->images()->create([
            'path' => 'path/to/image.jpg',
            'order_column' => 1,
        ]);

        $this->assertCount(1, $item->images);
        $this->assertEquals('path/to/image.jpg', $item->images->first()->path);
    }

    #[Test]
    public function a_tenant_can_have_multiple_items(): void
    {
        $tenant = Tenant::factory()
            ->has(Item::factory()->count(3))
            ->create();

        $this->assertCount(3, $tenant->items);
    }

    #[Test]
    public function an_item_can_belong_to_multiple_collections(): void
    {
        $item = Item::factory()->create();
        $collection1 = Collection::factory()->create(['tenant_id' => $item->tenant_id]);
        $collection2 = Collection::factory()->create(['tenant_id' => $item->tenant_id]);

        $item->collections()->attach([$collection1->id, $collection2->id]);

        $this->assertCount(2, $item->fresh()->collections);
    }

    #[Test]
    public function an_item_can_have_attributes_with_values(): void
    {
        $item = Item::factory()->create();
        $attribute = SharedAttribute::factory()->create();
        $value = '1984';

        // THE FIX: Use the renamed relationship 'customAttributes'
        $item->customAttributes()->attach($attribute->id, ['value' => $value]);

        $this->assertCount(1, $item->fresh()->customAttributes);
        $this->assertEquals($value, $item->fresh()->customAttributes->first()->pivot->value);
    }
}
