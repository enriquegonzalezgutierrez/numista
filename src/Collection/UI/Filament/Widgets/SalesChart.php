<?php

namespace Numista\Collection\UI\Filament\Widgets;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\Tenant;

class SalesChart extends ChartWidget
{
    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 0;

    public function getHeading(): string
    {
        return __('panel.widget_chart_sales_by_month');
    }

    protected function getData(): array
    {
        /** @var Tenant $currentTenant */
        $currentTenant = Filament::getTenant();

        $data = Order::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('created_at', '>=', now()->subYear())
            ->select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $labels[] = now()->subMonths($i)->format('Y-m');
        }

        $chartData = [];
        foreach ($labels as $label) {
            $chartData[] = $data[$label] ?? 0;
        }

        $displayLabels = collect($labels)->map(fn ($label) => Carbon::createFromFormat('Y-m', $label)->translatedFormat('M Y'))->all();

        return [
            'datasets' => [
                [
                    'label' => __('panel.widget_chart_sales_dataset_label'),
                    'data' => $chartData,
                    'backgroundColor' => 'rgba(20, 184, 166, 0.2)',
                    'borderColor' => 'rgb(20, 184, 166)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $displayLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
