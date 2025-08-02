<?php

// src/Collection/UI/Filament/Resources/CustomerResource/RelationManagers/AddressesRelationManager.php

namespace Numista\Collection\UI\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.label_addresses');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]); // Read-only
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                // THE FIX: Use translation keys for all column labels.
                Tables\Columns\TextColumn::make('label')->label(__('panel.field_address_label')),
                Tables\Columns\TextColumn::make('recipient_name')->label(__('panel.field_recipient_name')),
                Tables\Columns\TextColumn::make('street_address')->label(__('panel.field_street_address')),
                Tables\Columns\TextColumn::make('city')->label(__('panel.field_city')),
                Tables\Columns\TextColumn::make('postal_code')->label(__('panel.field_postal_code')),
                Tables\Columns\IconColumn::make('is_default')->boolean()->label(__('panel.field_is_default')),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
