<?php

// tests/Feature/Filament/ItemImagesRelationManagerTest.php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Resources\ItemResource\Pages\EditItem;
use Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers\ImagesRelationManager;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemImagesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private Tenant $tenant;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('tenants');

        $this->tenant = Tenant::factory()->create();
        $this->adminUser = User::factory()->admin()->create();
        $this->adminUser->tenants()->attach($this->tenant);

        $this->actingAs($this->adminUser);
        Filament::setTenant($this->tenant);

        $this->item = Item::factory()->create(['tenant_id' => $this->tenant->id]);
    }

    #[Test]
    public function it_can_render_the_images_relation_manager(): void
    {
        $this->item->images()->create(['path' => 'image.jpg']);

        Livewire::test(ImagesRelationManager::class, [
            'ownerRecord' => $this->item,
            'pageClass' => EditItem::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords($this->item->images);
    }

    #[Test]
    public function it_can_upload_an_image(): void
    {
        $file = UploadedFile::fake()->image('new-photo.jpg');

        Livewire::test(ImagesRelationManager::class, [
            'ownerRecord' => $this->item,
            'pageClass' => EditItem::class,
        ])
            ->callTableAction('create', data: [
                'path' => [$file],
                'alt_text' => 'An alternative text for the new photo.',
            ])
            ->assertHasNoTableActionErrors();

        $directory = 'tenant-'.$this->tenant->id.'/item-images';
        $files = Storage::disk('tenants')->files($directory);
        $this->assertCount(1, $files);

        $newFilePath = Arr::first($files);

        $this->assertDatabaseHas('images', [
            'imageable_id' => $this->item->id,
            'imageable_type' => Item::class,
            'path' => $newFilePath,
            'alt_text' => 'An alternative text for the new photo.',
        ]);
    }
}
