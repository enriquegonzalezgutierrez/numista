<?php

// src/Collection/UI/Filament/Resources/CustomerResource/Pages/ViewCustomer.php

namespace Numista\Collection\UI\Filament\Resources\CustomerResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\UI\Filament\Resources\CustomerResource;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    /**
     * This hook is reliably called on the view page.
     * We use it to ensure all necessary relationships are loaded.
     */
    protected function mutateRecord(Model $record): Model
    {
        return $record->load('user');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
