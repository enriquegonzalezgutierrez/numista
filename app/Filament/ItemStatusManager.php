<?php
// app/Filament/ItemStatusManager.php

namespace App\Filament;

// Import all status classes
use App\Filament\ItemStatuses\DiscountedStatus;
use App\Filament\ItemStatuses\FeaturedStatus;
use App\Filament\ItemStatuses\ForSaleStatus;
use App\Filament\ItemStatuses\InCollectionStatus;
use App\Filament\ItemStatuses\SoldStatus;

class ItemStatusManager
{
    /**
     * The single source of truth for all available item statuses.
     *
     * @var array<string, class-string>
     */
    protected array $statuses = [
        'in_collection' => InCollectionStatus::class,
        'for_sale' => ForSaleStatus::class,
        'featured' => FeaturedStatus::class,
        'discounted' => DiscountedStatus::class,
        'sold' => SoldStatus::class,
    ];

    /**
     * Dynamically generates the list of statuses for a Select field.
     *
     * @return array<string, string>
     */
    public function getStatusesForSelect(): array
    {
        $statusKeys = array_keys($this->statuses);

        $translatedStatuses = [];
        foreach ($statusKeys as $statusKey) {
            $translatedStatuses[$statusKey] = __('item.status_' . $statusKey);
        }

        return $translatedStatuses;
    }

    /**
     * Get the translated label for a single status key.
     *
     * @param string $statusKey The key of the status (e.g., 'for_sale').
     * @return string The translated status label.
     */
    public function getTranslatedStatus(string $statusKey): string
    {
        // Check if the key exists to avoid errors with old data
        if (!array_key_exists($statusKey, $this->statuses)) {
            return ucfirst($statusKey); // Fallback to a readable version of the key
        }

        return __('item.status_' . $statusKey);
    }
}
