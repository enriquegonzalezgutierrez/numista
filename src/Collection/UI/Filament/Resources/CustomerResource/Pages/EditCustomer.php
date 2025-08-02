<?php

namespace Numista\Collection\UI\Filament\Resources\CustomerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Numista\Collection\UI\Filament\Resources\CustomerResource;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
