<?php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class ValuableItemsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        /** @var Tenant $currentTenant */
        $currentTenant = Filament::getTenant();

        return $table
            ->heading(__('panel.widget_table_valuable_items'))
            ->query(
                Item::query()
                    ->where('tenant_id', $currentTenant->id)
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
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('panel.widget_table_view_action'))
                    ->url(fn (Item $record): string => ItemResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
