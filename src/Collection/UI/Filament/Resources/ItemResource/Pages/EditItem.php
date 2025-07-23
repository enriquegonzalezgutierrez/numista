<?php

// src/Collection/UI/Filament/Resources/ItemResource/Pages/EditItem.php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $data = $this->getRecord()->toArray();

        $data['attributes'] = $this->getRecord()->attributes->mapWithKeys(function ($attribute) {
            $pivot = $attribute->pivot;
            $payload = [
                'value' => $pivot->value,
                'attribute_value_id' => $pivot->attribute_value_id,
            ];

            return [$attribute->id => $payload];
        })->toArray();

        $this->form->fill($data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Item $record */
        $record->update($data);

        if (isset($data['attributes'])) {
            $this->syncAttributes($record, $data['attributes']);
        }

        return $record;
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
