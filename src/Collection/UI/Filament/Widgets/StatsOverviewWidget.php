<?php

// src/Collection/UI/Filament/Widgets/StatsOverviewWidget.php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        /** @var Tenant $currentTenant */
        $currentTenant = Filament::getTenant();

        $cacheKey = "widgets:stats_overview:tenant_{$currentTenant->id}";

        $stats = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($currentTenant) {

            // THE FIX: To count categories used by the tenant, we query through the items.
            // We count the distinct categories associated with items belonging to the current tenant.
            $usedCategoriesCount = Category::query()
                ->whereHas('items', function ($query) use ($currentTenant) {
                    $query->where('tenant_id', $currentTenant->id);
                })
                ->distinct()
                ->count();

            return [
                'totalItems' => Item::where('tenant_id', $currentTenant->id)->count(),
                'totalValue' => Item::where('tenant_id', $currentTenant->id)->sum('purchase_price'),
                'itemsForSale' => Item::where('tenant_id', $currentTenant->id)->where('status', 'for_sale')->count(),
                'totalCategories' => $usedCategoriesCount, // Use the new calculated count
                'totalCollections' => Collection::where('tenant_id', $currentTenant->id)->count(),
            ];
        });

        return [
            Stat::make(__('panel.widget_stats_total_items'), $stats['totalItems'])
                ->description(__('panel.widget_stats_total_items_desc'))
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('success'),

            Stat::make(__('panel.widget_stats_collection_value'), number_format($stats['totalValue'], 2).' €')
                ->description(__('panel.widget_stats_collection_value_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make(__('panel.widget_stats_items_for_sale'), $stats['itemsForSale'])
                ->description(__('panel.widget_stats_items_for_sale_desc'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make(__('panel.widget_stats_categories'), $stats['totalCategories'])
                ->description(__('panel.widget_stats_categories_desc'))
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make(__('panel.widget_stats_collections'), $stats['totalCollections'])
                ->description(__('panel.widget_stats_collections_desc'))
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('info'),
        ];
    }
}
