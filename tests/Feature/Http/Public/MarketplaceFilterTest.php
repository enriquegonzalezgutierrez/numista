<?php

// tests/Feature/Http/Public/MarketplaceFilterTest.php

namespace Tests\Feature\Http\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketplaceFilterTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Attribute $yearAttribute;

    private Attribute $gradeAttribute;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a common tenant for all tests
        $this->tenant = Tenant::factory()->create();

        // Create common attributes to be used in tests
        $this->yearAttribute = Attribute::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Year',
            'type' => 'number',
            'is_filterable' => true,
        ]);

        $this->gradeAttribute = Attribute::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Grade',
            'type' => 'select',
            'is_filterable' => true,
        ]);
    }

    #[Test]
    public function it_can_filter_items_by_search_term(): void
    {
        // Arrange
        $itemToShow = Item::factory()->create(['name' => 'Unique Silver Coin', 'status' => 'for_sale', 'tenant_id' => $this->tenant->id]);
        $itemToHide = Item::factory()->create(['name' => 'Generic Gold Coin', 'status' => 'for_sale', 'tenant_id' => $this->tenant->id]);

        // Act
        $response = $this->get(route('public.items.index', ['search' => 'Silver']));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($itemToShow->name);
        $response->assertDontSee($itemToHide->name);
    }

    #[Test]
    public function it_can_filter_items_by_category(): void
    {
        // Arrange
        $categoryA = Category::factory()->create(['tenant_id' => $this->tenant->id]);
        $itemInCategoryA = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id]);
        $itemInCategoryA->categories()->attach($categoryA);

        $itemWithoutCategory = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id]);

        // Act
        $response = $this->get(route('public.items.index', ['categories' => [$categoryA->id]]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($itemInCategoryA->name);
        $response->assertDontSee($itemWithoutCategory->name);
    }

    #[Test]
    public function it_can_filter_items_by_a_text_attribute(): void
    {
        // Arrange
        $item1990 = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id]);
        $item1990->attributes()->attach($this->yearAttribute->id, ['value' => '1990']);

        $item2005 = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id]);
        $item2005->attributes()->attach($this->yearAttribute->id, ['value' => '2005']);

        // Act
        $response = $this->get(route('public.items.index', ['attributes' => [$this->yearAttribute->id => '1990']]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($item1990->name);
        $response->assertDontSee($item2005->name);
    }

    #[Test]
    public function it_can_filter_items_by_a_select_attribute(): void
    {
        // Arrange
        $uncOption = $this->gradeAttribute->values()->create(['value' => 'unc']);
        $vfOption = $this->gradeAttribute->values()->create(['value' => 'vf']);

        $itemUnc = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id]);
        $itemUnc->attributes()->attach($this->gradeAttribute->id, ['value' => 'unc', 'attribute_value_id' => $uncOption->id]);

        $itemVf = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id]);
        $itemVf->attributes()->attach($this->gradeAttribute->id, ['value' => 'vf', 'attribute_value_id' => $vfOption->id]);

        // Act: We filter by the ID of the 'unc' option
        $response = $this->get(route('public.items.index', ['attributes' => [$this->gradeAttribute->id => $uncOption->id]]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee($itemUnc->name);
        $response->assertDontSee($itemVf->name);
    }
}
