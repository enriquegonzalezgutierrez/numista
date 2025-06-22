<?php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;

class StatsOverviewWidget extends BaseWidget
{
    /**
     * Get the stats for the overview widget.
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $totalItems = Item::count();
        $totalValue = Item::sum('purchase_price');
        $itemsForSale = Item::where('status', 'for_sale')->count();
        $totalCategories = Category::count();
        $totalCollections = Collection::count();

        return [
            Stat::make(__('panel.widget_stats_total_items'), $totalItems)
                ->description(__('panel.widget_stats_total_items_desc'))
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('success'),

            Stat::make(__('panel.widget_stats_collection_value'), number_format($totalValue, 2) . ' €')
                ->description(__('panel.widget_stats_collection_value_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make(__('panel.widget_stats_items_for_sale'), $itemsForSale)
                ->description(__('panel.widget_stats_items_for_sale_desc'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make(__('panel.widget_stats_categories'), $totalCategories)
                ->description(__('panel.widget_stats_categories_desc'))
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make(__('panel.widget_stats_collections'), $totalCollections)
                ->description(__('panel.widget_stats_collections_desc'))
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('info'),
        ];
    }
}
