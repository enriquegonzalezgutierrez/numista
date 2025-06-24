<?php

// tests/Feature/Domain/ModelRelationshipsTest.php

namespace Tests\Feature\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_item_can_have_categories_attached(): void
    {
        // Arrange: Create an item and two categories belonging to the same tenant
        $item = Item::factory()->create();
        $category1 = Category::factory()->create(['tenant_id' => $item->tenant_id]);
        $category2 = Category::factory()->create(['tenant_id' => $item->tenant_id]);

        // Act: Attach the categories to the item
        $item->categories()->attach([$category1->id, $category2->id]);

        // Assert: Verify that the item now has 2 categories
        $this->assertCount(2, $item->fresh()->categories);
        $this->assertTrue($item->categories->contains($category1));
    }

    #[Test]
    public function an_item_can_have_images(): void
    {
        // Arrange: Create an item
        $item = Item::factory()->create();

        // Act: Create an image specifically for this item
        $image = $item->images()->create([
            'path' => 'path/to/image.jpg',
            'order_column' => 1,
        ]);

        // Assert: Verify the item has one image and the path is correct
        $this->assertCount(1, $item->images);
        $this->assertEquals('path/to/image.jpg', $item->images->first()->path);
    }

    #[Test]
    public function a_tenant_can_have_multiple_items(): void
    {
        // Arrange: Use the 'has' magic method to create a tenant WITH 3 items
        $tenant = Tenant::factory()
            ->has(Item::factory()->count(3))
            ->create();

        // Assert: Verify the tenant's 'items' relationship returns 3 items
        $this->assertCount(3, $tenant->items);
    }

    #[Test]
    public function an_item_can_belong_to_multiple_collections(): void
    {
        // Arrange: Create an item and two collections from the same tenant
        $item = Item::factory()->create();
        $collection1 = Collection::factory()->create(['tenant_id' => $item->tenant_id]);
        $collection2 = Collection::factory()->create(['tenant_id' => $item->tenant_id]);

        // Act: Attach the item to both collections
        $item->collections()->attach([$collection1->id, $collection2->id]);

        // Assert: Verify the relationship
        $this->assertCount(2, $item->fresh()->collections);
    }
}
