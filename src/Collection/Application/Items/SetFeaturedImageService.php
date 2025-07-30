<?php

namespace Numista\Collection\Application\Items;

use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Image;

class SetFeaturedImageService
{
    /**
     * Set the featured status for an image, ensuring only one is featured per item.
     *
     * @param  \Numista\Collection\Domain\Models\Image  $image  The image to update.
     * @param  bool  $isFeatured  The new featured state.
     */
    public function handle(Image $image, bool $isFeatured): void
    {
        // Use a transaction to ensure data integrity.
        DB::transaction(function () use ($image, $isFeatured) {
            // If we are setting this image as featured...
            if ($isFeatured) {
                // ...first, un-feature all other images for the same parent item.
                $image->imageable->images()
                    ->where('id', '!=', $image->id)
                    ->update(['is_featured' => false]);
            }

            // Then, update the current image with the new state.
            $image->update(['is_featured' => $isFeatured]);
        });
    }
}
