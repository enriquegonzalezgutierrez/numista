<?php

namespace Numista\Collection\UI\Filament\Resources\SharedAttributeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Numista\Collection\UI\Filament\Resources\SharedAttributeResource;

class ListSharedAttributes extends ListRecords
{
    protected static string $resource = SharedAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
