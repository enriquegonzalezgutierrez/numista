<?php

namespace Numista\Collection\UI\Filament\Resources\CustomerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Numista\Collection\UI\Filament\Resources\CustomerResource;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
