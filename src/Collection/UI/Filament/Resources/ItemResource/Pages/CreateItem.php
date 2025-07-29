<?php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Application\Items\CreateItemService;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // THE FIX: Manually add the current tenant's ID to the data array.
        // This is necessary because we are using a custom service instead of the default
        // Eloquent creation method, which would handle this automatically.
        $data['tenant_id'] = Filament::getTenant()->id;

        $createItemService = app(CreateItemService::class);

        return $createItemService->handle($data);
    }
}
