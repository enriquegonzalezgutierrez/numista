<?php

// tests/Feature/Http/Public/MarketplaceFilterTest.php

namespace Tests\Feature\Http\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\ItemType; // THE FIX: Import the ItemType model
use Numista\Collection\Domain\Models\SharedAttribute; // THE FIX: Use the new SharedAttribute model
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketplaceFilterTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private SharedAttribute $yearAttribute;

    private SharedAttribute $gradeAttribute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();

        // THE FIX: We need to create ItemType records in the database first.
        $coinType = ItemType::factory()->create(['name' => 'coin']);

        // THE FIX: Use the new SharedAttribute model and remove tenant_id.
        $this->yearAttribute = SharedAttribute::factory()->create([
            'name' => 'Year',
            'type' => 'number',
            'is_filterable' => true,
        ]);
        // Link the attribute to the item type.
        $this->yearAttribute->itemTypes()->attach($coinType);

        $this->gradeAttribute = SharedAttribute::factory()->create([
            'name' => 'Grade',
            'type' => 'select',
            'is_filterable' => true,
        ]);
        $this->gradeAttribute->itemTypes()->attach($coinType);
    }

    #[Test]
    public function it_can_filter_items_by_search_term(): void
    {
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
        // THE FIX: Ensure the items are of a type that has the 'Year' attribute linked.
        $item1990 = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $item1990->attributes()->attach($this->yearAttribute->id, ['value' => '1990']);

        $item2005 = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $item2005->attributes()->attach($this->yearAttribute->id, ['value' => '2005']);

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

        // THE FIX: Ensure items are of 'coin' type.
        $itemUnc = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $itemUnc->attributes()->attach($this->gradeAttribute->id, ['value' => 'unc', 'attribute_option_id' => $uncOption->id]);

        $itemVf = Item::factory()->create(['status' => 'for_sale', 'tenant_id' => $this->tenant->id, 'type' => 'coin']);
        $itemVf->attributes()->attach($this->gradeAttribute->id, ['value' => 'vf', 'attribute_option_id' => $vfOption->id]);

        $response = $this->get(route('public.items.index', ['attributes' => [$this->gradeAttribute->id => $uncOption->id]]));

        $response->assertStatus(200);
        $response->assertSee($itemUnc->name);
        $response->assertDontSee($itemVf->name);
    }
}
