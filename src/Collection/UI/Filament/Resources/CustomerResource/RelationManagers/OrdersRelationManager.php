<?php

// src/Collection/UI/Filament/Resources/CustomerResource/RelationManagers/OrdersRelationManager.php

namespace Numista\Collection\UI\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.label_orders');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]); // Read-only
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_number')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label(__('panel.field_order_number'))->searchable(),
                Tables\Columns\TextColumn::make('status')->label(__('item.field_status'))->badge()->formatStateUsing(fn ($state) => __("item.status_{$state}")),
                Tables\Columns\TextColumn::make('total_amount')->label(__('panel.field_total_amount'))->money('eur'),
                Tables\Columns\TextColumn::make('created_at')->label(__('panel.field_order_date'))->dateTime(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => \Numista\Collection\UI\Filament\Resources\OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([]);
    }
}
