<?php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\Pages;

use Numista\Collection\UI\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
