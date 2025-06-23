<?php

namespace Numista\Collection\UI\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Numista\Collection\UI\Filament\Resources\ItemResource;
use Illuminate\Database\Eloquent\Model;

class ItemsRelationManager extends RelationManager
{
    /**
     * The relationship method on the parent model.
     * @var string
     */
    protected static string $relationship = 'items';

    /**
     * Get the translated title for this relation manager.
     * @param Model $ownerRecord
     * @param string $pageClass
     * @return string
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.label_order_items');
    }

    /**
     * The form is not used as we make actions unavailable.
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    /**
     * Defines the table structure for the order items.
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('item.name')
                    ->label(__('item.field_name'))
                    ->url(fn(Model $record): string => ItemResource::getUrl('edit', ['record' => $record->item_id]))
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
