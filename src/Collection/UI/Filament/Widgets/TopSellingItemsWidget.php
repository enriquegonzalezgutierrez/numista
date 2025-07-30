<?php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class TopSellingItemsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        /** @var Tenant $currentTenant */
        $currentTenant = Filament::getTenant();

        return $table
            ->heading(__('panel.widget_table_top_selling'))
            ->query(
                Item::query()
                    ->where('tenant_id', $currentTenant->id)
                    ->whereHas('orderItems')
                    ->withCount(['orderItems as total_sold' => function ($query) {
                        $query->select(DB::raw('sum(quantity)'));
                    }])
                    ->orderByDesc('total_sold')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('panel.widget_table_column_name')),

                Tables\Columns\TextColumn::make('total_sold')
                    ->label(__('panel.widget_table_column_units_sold'))
                    ->numeric()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('panel.widget_table_view_action'))
                    ->url(fn (Item $record): string => ItemResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
