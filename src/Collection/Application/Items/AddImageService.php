<?php

namespace Numista\Collection\Application\Items;

use Numista\Collection\Domain\Models\Image;
use Numista\Collection\Domain\Models\Item;

class AddImageService
{
    public function handle(Item $item, array $data): Image
    {
        return $item->images()->create($data);
    }
}
