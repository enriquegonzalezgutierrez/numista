<?php

namespace Tests\Feature\Http\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_landing_page_is_displayed(): void
    {
        $this->get('/')->assertStatus(200);
    }

    #[Test]
    public function landing_page_shows_featured_collections_and_latest_items(): void
    {
        $collection = Collection::factory()->create();
        $item = Item::factory()->create(['status' => 'for_sale']);

        $response = $this->get(route('landing'));

        $response->assertStatus(200);
        $response->assertViewHas('featuredCollections');
        $response->assertViewHas('latestItems');
        $response->assertSee($collection->name);
        $response->assertSee($item->name);
    }

    #[Test]
    public function landing_page_does_not_show_items_that_are_not_for_sale(): void
    {
        $itemNotForSale = Item::factory()->create(['status' => 'in_collection']);

        $this->get(route('landing'))
            ->assertStatus(200)
            ->assertDontSee($itemNotForSale->name);
    }
}
