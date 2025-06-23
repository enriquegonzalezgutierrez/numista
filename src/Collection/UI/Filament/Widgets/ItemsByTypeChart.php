<?php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\UI\Filament\ItemTypeManager;

class ItemsByTypeChart extends ChartWidget
{
    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('panel.widget_chart_items_by_type');
    }

    protected function getData(): array
    {
        $data = Item::query()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->all();

        $manager = new ItemTypeManager;
        $allPossibleTypes = $manager->getTypesForSelect();

        $translatedLabels = [];
        foreach (array_keys($data) as $typeKey) {
            $translatedLabels[] = $allPossibleTypes[$typeKey] ?? ucfirst($typeKey);
        }

        return [
            'datasets' => [
                [
                    'label' => __('panel.label_items'),
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#14b8a6', '#f59e0b', '#3b82f6', '#ef4444',
                        '#8b5cf6', '#ec4899', '#64748b', '#22c55e',
                    ],
                ],
            ],
            'labels' => $translatedLabels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
