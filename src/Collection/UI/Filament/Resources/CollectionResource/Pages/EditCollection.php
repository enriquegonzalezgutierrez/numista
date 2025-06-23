<?php

namespace Numista\Collection\UI\Filament\Resources\CollectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Numista\Collection\UI\Filament\Resources\CollectionResource;

class EditCollection extends EditRecord
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
