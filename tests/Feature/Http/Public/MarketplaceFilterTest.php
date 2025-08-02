<?php

// tests/Feature/Http/Public/MarketplaceFilterTest.php

namespace Tests\Feature\Http\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\ItemType;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketplaceFilterTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private SharedAttribute $yearAttribute;

    private SharedAttribute $gradeAttribute;

    private ItemType $coinType;

    protected function setUp(): void
    {
        parent::setUp();
        // It's a good practice to refresh Scout's index for each test.
        $this->artisan('scout:flush', ['model' => Item::class]);

        $this->tenant = Tenant::factory()->create();
        $this->coinType = ItemType::factory()->create(['name' => 'coin']);

        $this->yearAttribute = SharedAttribute::factory()->create(['name' => 'Year', 'type' => 'number', 'is_filterable' => true]);
        $this->yearAttribute->itemTypes()->attach($this->coinType);

        $this->gradeAttribute = SharedAttribute::factory()->create(['name' => 'Grade', 'type' => 'select', 'is_filterable' => true]);
        $this->gradeAttribute->itemTypes()->attach($this->coinType);
    }

    #[Test]
    public function it_can_filter_items_by_search_term(): void
    {
        // Items are automatically indexed on creation thanks to the Searchable trait.
        $itemToShow = Item::factory()->create(['name' => 'Unique Silver Coin', 'status' => 'for_sale', 'tenant_id' => $this->tenant->id]);
        $itemToHide = Item::factory()->create(['name' => 'Generic Gold Coin', 'status' => 'for_sale', 'tenant_id' => $this->tenant->id]);

        $response = $this->get(route('public.items.index', ['search' => 'Silver']));

        $response->assertStatus(200);
        $response->assertSee($itemToShow->name);
        $response->assertDontSee($itemToHide->name);
    }

    #[Test]
    public function it_can_filter_items_by_a_text_attribute(): void
    {
        $item1990 = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $item1990->customAttributes()->attach($this->yearAttribute->id, ['value' => '1990']);
        $item1990->searchable(); // Manually re-sync after attaching attributes

        $item2005 = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $item2005->customAttributes()->attach($this->yearAttribute->id, ['value' => '2005']);
        $item2005->searchable(); // Manually re-sync

        $response = $this->get(route('public.items.index', ['attributes' => [$this->yearAttribute->id => '1990']]));

        $response->assertStatus(200);
        $response->assertSee($item1990->name);
        $response->assertDontSee($item2005->name);
    }

    #[Test]
    public function it_can_filter_items_by_a_select_attribute(): void
    {
        $uncOption = $this->gradeAttribute->options()->create(['value' => 'unc']);
        $vfOption = $this->gradeAttribute->options()->create(['value' => 'vf']);

        $itemUnc = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $itemUnc->customAttributes()->attach($this->gradeAttribute->id, ['value' => 'unc', 'attribute_option_id' => $uncOption->id]);
        $itemUnc->searchable(); // Manually re-sync

        $itemVf = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $itemVf->customAttributes()->attach($this->gradeAttribute->id, ['value' => 'vf', 'attribute_option_id' => $vfOption->id]);
        $itemVf->searchable(); // Manually re-sync

        $response = $this->get(route('public.items.index', ['attributes' => [$this->gradeAttribute->id => $uncOption->id]]));

        $response->assertStatus(200);
        $response->assertSee($itemUnc->name);
        $response->assertDontSee($itemVf->name);
    }
}
