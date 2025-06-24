<?php

// src/Collection/UI/Filament/Resources/OrderResource/Pages/ViewOrder.php

namespace Numista\Collection\UI\Filament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\UI\Filament\Resources\OrderResource;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Get the record for the page.
     * This overrides the default method to include eager loading.
     */
    protected function resolveRecord(int|string $key): Model
    {
        // Get the Order model directly and eager load the customer.
        // This is the most direct and robust way.
        return Order::with('customer')->findOrFail($key);
    }

    /**
     * Get the translated title for the page.
     */
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
