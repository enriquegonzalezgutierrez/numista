<?php

// tests/Feature/Filament/ItemResourceBulkActionsTest.php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Resources\ItemResource\Pages\ListItems;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemResourceBulkActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->adminUser = User::factory()->admin()->create();
        $this->adminUser->tenants()->attach($this->tenant);

        $this->actingAs($this->adminUser);
        Filament::setTenant($this->tenant);
    }

    #[Test]
    public function it_can_bulk_change_the_status_of_items(): void
    {
        // Arrange: Create a collection of items, all with the 'in_collection' status.
        $items = Item::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'status' => 'in_collection',
        ]);

        // Act: Simulate selecting these items and running the 'change_status' bulk action.
        Livewire::test(ListItems::class)
            ->callTableBulkAction('change_status', $items, data: [
                'status' => 'for_sale',
            ]);

        // Assert: Verify that each item's status has been updated in the database.
        foreach ($items as $item) {
            $this->assertEquals('for_sale', $item->fresh()->status);
        }
    }
}
