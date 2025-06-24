<?php

namespace Numista\Collection\UI\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\UI\Filament\Resources\ItemResource;

class ItemsRelationManager extends RelationManager
{
    /**
     * The relationship method on the parent model.
     */
    protected static string $relationship = 'items';

    /**
     * Get the translated title for this relation manager.
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.label_order_items');
    }

    /**
     * The form is not used as we make actions unavailable.
     */
    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    /**
     * Defines the table structure for the order items.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('item.name')
                    ->label(__('item.field_name'))
                    ->url(fn (Model $record): string => ItemResource::getUrl('edit', ['record' => $record->item_id]))
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label(__('item.field_quantity')),

                TextColumn::make('price')
                    ->label(__('item.field_unit_price'))
                    ->money('eur'),
            ])
            // Disable all actions to make it read-only
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
