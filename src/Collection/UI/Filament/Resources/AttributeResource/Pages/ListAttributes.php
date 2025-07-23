<?php

namespace Numista\Collection\UI\Filament\Resources\AttributeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Numista\Collection\UI\Filament\Resources\AttributeResource;

class ListAttributes extends ListRecords
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
