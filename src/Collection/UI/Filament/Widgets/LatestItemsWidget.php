<?php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class LatestItemsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        /** @var Tenant $currentTenant */
        $currentTenant = Filament::getTenant();

        return $table
            ->heading(__('panel.widget_table_latest_items'))
            ->query(
                Item::query()
                    ->with('images')
                    ->where('tenant_id', $currentTenant->id)
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('panel.widget_table_column_name')),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('panel.widget_table_column_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __("item.type_{$state}")),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('panel.widget_table_added_at'))
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('panel.widget_table_view_action'))
                    ->url(fn (Item $record): string => ItemResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
