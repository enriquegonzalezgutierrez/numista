<?php

namespace Numista\Collection\Application\Items;

use Numista\Collection\Domain\Models\AttributeValue;
use Numista\Collection\Domain\Models\Item;

class CreateItemService
{
    public function handle(array $data): Item
    {
        $attributesData = $data['attributes'] ?? [];
        unset($data['attributes']);

        $item = Item::create($data);

        if (! empty($attributesData)) {
            $this->syncAttributes($item, $attributesData);
        }

        return $item;
    }

    private function syncAttributes(Item $item, array $attributesData): void
    {
        $syncData = [];
        foreach ($attributesData as $attributeId => $data) {
            $value = null;
            $attributeValueId = null;

            if (isset($data['attribute_value_id']) && $data['attribute_value_id']) {
                $attributeValueId = $data['attribute_value_id'];
                $value = AttributeValue::find($attributeValueId)?->value;
            } elseif (isset($data['value'])) {
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
