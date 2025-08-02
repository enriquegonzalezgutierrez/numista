<?php

// src/Collection/UI/Filament/Resources/OrderResource/Pages/ViewOrder.php

namespace Numista\Collection\UI\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Numista\Collection\UI\Filament\Resources\OrderResource;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function fillForm(): void
    {
        $this->record->loadMissing('customer');
        $data = $this->record->toArray();
        $this->form->fill($data);
    }

    public function getTitle(): string|Htmlable
    {
        return __('panel.label_order').' #'.$this->record->order_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
