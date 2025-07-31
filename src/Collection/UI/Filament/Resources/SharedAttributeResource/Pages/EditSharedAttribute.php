<?php

// src/Collection/UI/Filament/Resources/SharedAttributeResource/Pages/EditSharedAttribute.php

namespace Numista\Collection\UI\Filament\Resources\SharedAttributeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Numista\Collection\UI\Filament\Resources\SharedAttributeResource;

class EditSharedAttribute extends EditRecord
{
    protected static string $resource = SharedAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
