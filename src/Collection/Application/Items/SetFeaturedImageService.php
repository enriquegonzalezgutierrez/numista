<?php

namespace Numista\Collection\Application\Items;

use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Image;

class SetFeaturedImageService
{
    public function handle(Image $image, bool $isFeatured): void
    {
        if (! $isFeatured) {
            $image->update(['is_featured' => false]);

            return;
        }

        DB::transaction(function () use ($image) {
            $image->imageable->images()->update(['is_featured' => false]);
            $image->update(['is_featured' => true]);
        });
    }
}
