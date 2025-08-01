<?php

// tests/Feature/Filament/ItemResourceTest.php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\ItemType;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Resources\ItemResource;
use Numista\Collection\UI\Filament\Resources\ItemResource\Pages\ListItems;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // THE FIX: Create a tenant that has an active subscription.
        // This will satisfy the CheckSubscriptionStatus middleware.
        $this->tenant = Tenant::factory()->create([
            'subscription_status' => 'active',
        ]);

        $this->adminUser = User::factory()->admin()->create();
        $this->adminUser->tenants()->attach($this->tenant);
        $this->actingAs($this->adminUser);
        Filament::setTenant($this->tenant);
    }

    #[Test]
    public function it_can_render_the_list_items_page(): void
    {
        $this->get(ItemResource::getUrl('index'))->assertSuccessful();
    }

    #[Test]
    public function it_can_list_items_belonging_to_the_current_tenant(): void
    {
        $itemInTenant = Item::factory()->create(['tenant_id' => $this->tenant->id]);
        Livewire::test(ListItems::class)
            ->assertCanSeeTableRecords([$itemInTenant]);
    }

    #[Test]
    public function it_cannot_list_items_from_another_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $itemInOtherTenant = Item::factory()->create(['tenant_id' => $otherTenant->id]);

        Livewire::test(ListItems::class)
            ->assertCanNotSeeTableRecords([$itemInOtherTenant]);
    }

    #[Test]
    public function it_can_render_the_create_item_page(): void
    {
        $this->get(ItemResource::getUrl('create'))->assertSuccessful();
    }

    #[Test]
    public function it_can_create_an_item_with_attributes(): void
    {
        $coinType = ItemType::factory()->create(['name' => 'coin']);
        $attribute = SharedAttribute::factory()->create(['type' => 'text', 'name' => 'Material']);
        $attribute->itemTypes()->attach($coinType);

        $newItemData = Item::factory()->raw([
            'type' => 'coin',
        ]);
        unset($newItemData['tenant_id'], $newItemData['slug']);

        $newItemData['attributes'] = [
            $attribute->id => ['value' => 'Silver'],
        ];

        Livewire::test(ItemResource\Pages\CreateItem::class)
            ->fillForm($newItemData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('items', [
            'name' => $newItemData['name'],
            'tenant_id' => $this->tenant->id,
        ]);

        $item = Item::where('name', $newItemData['name'])->first();
        $this->assertDatabaseHas('item_attribute', [
            'item_id' => $item->id,
            'shared_attribute_id' => $attribute->id,
            'value' => 'Silver',
        ]);
    }
}
