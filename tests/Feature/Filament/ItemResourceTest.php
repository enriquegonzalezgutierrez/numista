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
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private Tenant $tenant;

    private ItemType $coinType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create(['subscription_status' => 'active']);
        $this->adminUser = User::factory()->admin()->create();
        $this->adminUser->tenants()->attach($this->tenant);
        $this->actingAs($this->adminUser);
        Filament::setTenant($this->tenant);

        // Create the ItemType for tests that need it.
        $this->coinType = ItemType::factory()->create(['name' => 'coin']);
    }

    #[Test]
    public function it_can_create_an_item_with_attributes(): void
    {
        $attribute = SharedAttribute::factory()->create(['type' => 'text', 'name' => 'Material']);
        $attribute->itemTypes()->attach($this->coinType);
        $newItemData = Item::factory()->raw(['type' => 'coin']);
        unset($newItemData['tenant_id'], $newItemData['slug']);
        $newItemData['attributes'] = [$attribute->id => ['value' => 'Silver']];

        Livewire::test(ItemResource\Pages\CreateItem::class)
            ->fillForm($newItemData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('items', ['name' => $newItemData['name'], 'tenant_id' => $this->tenant->id]);
        $item = Item::where('name', $newItemData['name'])->first();
        $this->assertDatabaseHas('item_attribute', ['item_id' => $item->id, 'shared_attribute_id' => $attribute->id, 'value' => 'Silver']);
    }

    #[Test]
    public function it_can_update_an_item_with_attributes(): void
    {
        $item = Item::factory()->create(['tenant_id' => $this->tenant->id, 'name' => 'Old Name', 'type' => 'coin']);

        // THE FIX: Explicitly create a 'text' attribute to avoid validation errors.
        $attribute = SharedAttribute::factory()->create(['type' => 'text']);
        $attribute->itemTypes()->attach($this->coinType);
        $item->customAttributes()->attach($attribute->id, ['value' => 'Old Value']);

        Livewire::test(ItemResource\Pages\EditItem::class, ['record' => $item->getRouteKey()])
            ->fillForm([
                'name' => 'New Name',
                'type' => 'coin',
                'attributes' => [
                    $attribute->id => ['value' => 'New Value'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'New Name']);
        $this->assertDatabaseHas('item_attribute', ['item_id' => $item->id, 'value' => 'New Value']);
        $this->assertDatabaseMissing('item_attribute', ['item_id' => $item->id, 'value' => 'Old Value']);
    }

    #[Test]
    public function a_tenant_cannot_access_another_tenants_items_in_panel(): void
    {
        $tenantB = Tenant::factory()->create(['subscription_status' => 'active']);
        $userB = User::factory()->admin()->create();
        $userB->tenants()->attach($tenantB);
        $itemOfTenantB = Item::factory()->create(['tenant_id' => $tenantB->id]);

        $this->actingAs($this->adminUser);
        Filament::setTenant($this->tenant);
        $response = $this->get(ItemResource::getUrl('edit', ['record' => $itemOfTenantB]));
        $response->assertNotFound();
    }
}
