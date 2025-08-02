<?php

// tests/Unit/Application/Items/SetFeaturedImageServiceTest.php

namespace Tests\Unit\Application\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Application\Items\SetFeaturedImageService;
use Numista\Collection\Domain\Models\Item;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SetFeaturedImageServiceTest extends TestCase
{
    use RefreshDatabase;

    private SetFeaturedImageService $service;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SetFeaturedImageService;
        $this->item = Item::factory()->create();
    }

    #[Test]
    public function it_sets_an_image_as_featured(): void
    {
        // Arrange: Create an image that is not featured.
        $image = $this->item->images()->create(['path' => 'image.jpg', 'is_featured' => false]);

        // Act: Call the service to set it as featured.
        $this->service->handle($image, true);

        // Assert: The image is now featured in the database.
        $this->assertDatabaseHas('images', [
            'id' => $image->id,
            'is_featured' => true,
        ]);
    }

    #[Test]
    public function setting_a_new_image_as_featured_unsets_the_previous_one(): void
    {
        // Arrange: Create two images, one already featured.
        $oldFeaturedImage = $this->item->images()->create(['path' => 'old.jpg', 'is_featured' => true]);
        $newFeaturedImage = $this->item->images()->create(['path' => 'new.jpg', 'is_featured' => false]);

        // Act: Set the new image as featured.
        $this->service->handle($newFeaturedImage, true);

        // Assert: The new image is featured, and the old one is not.
        $this->assertDatabaseHas('images', [
            'id' => $newFeaturedImage->id,
            'is_featured' => true,
        ]);

        $this->assertDatabaseHas('images', [
            'id' => $oldFeaturedImage->id,
            'is_featured' => false,
        ]);
    }

    #[Test]
    public function unsetting_an_image_as_featured_works_correctly(): void
    {
        // Arrange: Create a featured image.
        $image = $this->item->images()->create(['path' => 'image.jpg', 'is_featured' => true]);

        // Act: Call the service to un-feature it.
        $this->service->handle($image, false);

        // Assert: The image is no longer featured.
        $this->assertDatabaseHas('images', [
            'id' => $image->id,
            'is_featured' => false,
        ]);
    }
}
