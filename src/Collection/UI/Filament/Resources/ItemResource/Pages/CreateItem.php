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
        $data['tenant_id'] = Filament::getTenant()->id;

        $createItemService = app(CreateItemService::class);

        return $createItemService->handle($data);
    }
}
