<?php

// src/Collection/Application/Items/UpdateItemService.php

namespace Numista\Collection\Application\Items;

use Numista\Collection\Domain\Models\AttributeOption;
use Numista\Collection\Domain\Models\Item;

class UpdateItemService
{
    public function handle(Item $item, array $data): Item
    {
        $attributesData = $data['attributes'] ?? [];
        unset($data['attributes']);

        $item->update($data);

        $this->syncAttributes($item, $attributesData);

        return $item;
    }

    private function syncAttributes(Item $item, array $attributesData): void
    {
        $syncData = [];
        foreach ($attributesData as $attributeId => $data) {
            $value = null;
            $attributeOptionId = null;

            if (isset($data['attribute_option_id']) && $data['attribute_option_id']) {
                $attributeOptionId = $data['attribute_option_id'];
                $value = AttributeOption::find($attributeOptionId)?->value;
            } elseif (isset($data['value'])) {
                $value = $data['value'];
            }

            if ($value !== null && $value !== '') {
                $syncData[$attributeId] = [
                    'value' => $value,
                    'attribute_option_id' => $attributeOptionId,
                ];
            }
        }
        $item->attributes()->sync($syncData);
    }
}
