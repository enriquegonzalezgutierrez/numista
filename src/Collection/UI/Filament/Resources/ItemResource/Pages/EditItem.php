<?php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Application\Items\UpdateItemService;
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
        $updateItemService = app(UpdateItemService::class);

        return $updateItemService->handle($record, $data);
    }
}
