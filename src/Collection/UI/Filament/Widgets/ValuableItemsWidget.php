<?php

// src/Collection/UI/Filament/Widgets/ValuableItemsWidget.php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class ValuableItemsWidget extends BaseWidget
{
    /**
     * The sort order for the widget.
     * We want this to appear after the chart, but before the latest items.
     * @var int|null
     */
    protected static ?int $sort = 2;

    /**
     * Make the widget span the full width of the dashboard.
     * @var int|string|array
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Get the translated heading for the widget.
     * @return string
     */
    public function getHeading(): string
    {
        return __('panel.widget_table_valuable_items');
    }

    /**
     * Defines the table structure for the widget.
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('panel.widget_table_valuable_items'))
            ->query(
                // Query for items that are for sale, ordered by sale price descending
                ItemResource::getEloquentQuery()
                    ->where('status', 'for_sale')
                    ->whereNotNull('sale_price')
                    ->orderBy('sale_price', 'desc')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('item.field_name')),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label(__('panel.widget_table_column_sale_price'))
                    ->money('eur')
                    ->sortable(), // Although not paginated, good practice
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('panel.widget_table_view_action'))
                    ->url(fn(Item $record): string => ItemResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
