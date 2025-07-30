<?php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\Tenant;

class SalesOverviewWidget extends BaseWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        /** @var Tenant $currentTenant */
        $currentTenant = Filament::getTenant();
        $cacheKey = "widgets:sales_overview:tenant_{$currentTenant->id}";

        $stats = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($currentTenant) {
            $thirtyDaysAgo = now()->subDays(30);
            $query = Order::where('tenant_id', $currentTenant->id)->where('created_at', '>=', $thirtyDaysAgo);

            return [
                'revenue' => (clone $query)->sum('total_amount'),
                'ordersCount' => (clone $query)->count(),
                'avgOrderValue' => (clone $query)->avg('total_amount') ?? 0,
            ];
        });

        return [
            Stat::make(__('panel.widget_sales_revenue'), number_format($stats['revenue'], 2).' €')
                ->description(__('panel.widget_sales_revenue_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make(__('panel.widget_sales_orders'), $stats['ordersCount'])
                ->description(__('panel.widget_sales_orders_desc'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
            Stat::make(__('panel.widget_sales_avg_value'), number_format($stats['avgOrderValue'], 2).' €')
                ->description(__('panel.widget_sales_avg_value_desc'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}
