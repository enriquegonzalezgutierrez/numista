<?php

namespace Numista\Collection\UI\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Numista\Collection\UI\Filament\Widgets\ItemsByTypeChart;
use Numista\Collection\UI\Filament\Widgets\LatestItemsWidget;
use Numista\Collection\UI\Filament\Widgets\SalesChart;
use Numista\Collection\UI\Filament\Widgets\SalesOverviewWidget;
use Numista\Collection\UI\Filament\Widgets\StatsOverviewWidget;
use Numista\Collection\UI\Filament\Widgets\SubscriptionStatusWidget;
use Numista\Collection\UI\Filament\Widgets\TopSellingItemsWidget;
use Numista\Collection\UI\Filament\Widgets\ValuableItemsWidget;

class Dashboard extends BaseDashboard
{
    /**
     * Get the widgets displayed on the dashboard.
     *
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            SubscriptionStatusWidget::class,
            SalesOverviewWidget::class,
            StatsOverviewWidget::class,
            SalesChart::class,
            ItemsByTypeChart::class,
            TopSellingItemsWidget::class,
            ValuableItemsWidget::class,
            LatestItemsWidget::class,
        ];
    }
}
