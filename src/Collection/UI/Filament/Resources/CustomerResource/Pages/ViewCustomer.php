<?php

// src/Collection/UI/Filament/Resources/CustomerResource/Pages/ViewCustomer.php

namespace Numista\Collection\UI\Filament\Resources\CustomerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Numista\Collection\UI\Filament\Resources\CustomerResource;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    /**
     * THE FIX: Use fillForm() to ensure the `user` relationship is loaded
     * before the form is populated on the dedicated view page.
     */
    protected function fillForm(): void
    {
        $this->record->loadMissing('user');
        $data = $this->record->toArray();
        $this->form->fill($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
