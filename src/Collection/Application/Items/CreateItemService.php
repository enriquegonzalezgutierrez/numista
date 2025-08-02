<?php

// src/Collection/Application/Items/CreateItemService.php

namespace Numista\Collection\Application\Items;

use Numista\Collection\Domain\Models\AttributeOption;
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
        // THE FIX: Use the renamed relationship 'customAttributes'
        $item->customAttributes()->sync($syncData);
    }
}
