<?php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var Item $item */
        $item = static::getModel()::create($data);

        if (isset($data['attributes'])) {
            $this->syncAttributes($item, $data['attributes']);
        }

        return $item;
    }

    protected function syncAttributes(Item $item, array $attributesData): void
    {
        $syncData = [];
        foreach ($attributesData as $attributeId => $value) {
            // Only sync attributes that have a non-empty value
            if ($value !== null && $value !== '') {
                $syncData[$attributeId] = ['value' => $value];
            }
        }
        $item->attributes()->sync($syncData);
    }
}