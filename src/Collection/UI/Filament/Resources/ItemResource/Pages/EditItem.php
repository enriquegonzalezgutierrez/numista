<?php

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
    
    // This method loads the attribute data into the form
    protected function fillForm(): void
    {
        $data = $this->getRecord()->toArray();

        $data['attributes'] = $this->getRecord()->attributes->mapWithKeys(function ($attribute) {
            return [$attribute->id => $attribute->pivot->value];
        })->toArray();
        
        $this->form->fill($data);
    }

    // This method saves the core data and the attribute data
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
        foreach ($attributesData as $attributeId => $value) {
            if ($value !== null && $value !== '') {
                $syncData[$attributeId] = ['value' => $value];
            }
        }
        $item->attributes()->sync($syncData);
    }
}