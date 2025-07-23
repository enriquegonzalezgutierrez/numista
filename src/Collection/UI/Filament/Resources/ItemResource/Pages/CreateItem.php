<?php

// src/Collection/UI/Filament/Resources/ItemResource/Pages/CreateItem.php

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
        foreach ($attributesData as $attributeId => $data) {
            $value = null;
            $attributeValueId = null;

            if (isset($data['attribute_value_id'])) { // Is a 'select' type
                $attributeValueId = $data['attribute_value_id'];
                if ($attributeValueId) {
                    $value = \Numista\Collection\Domain\Models\AttributeValue::find($attributeValueId)?->value;
                }
            } elseif (isset($data['value'])) { // Is a text/date/number type
                $value = $data['value'];
            }

            if ($value !== null && $value !== '') {
                $syncData[$attributeId] = [
                    'value' => $value,
                    'attribute_value_id' => $attributeValueId,
                ];
            }
        }

        $item->attributes()->sync($syncData);
    }
}
