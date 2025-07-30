<?php

// src/Collection/UI/Filament/Resources/CustomerResource/RelationManagers/AddressesRelationManager.php

namespace Numista\Collection\UI\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Form $form): Form
    {
        return $form->schema([]); // Read-only
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')->label('Etiqueta'),
                Tables\Columns\TextColumn::make('recipient_name')->label('Destinatario'),
                Tables\Columns\TextColumn::make('street_address')->label('Dirección'),
                Tables\Columns\TextColumn::make('city')->label('Ciudad'),
                Tables\Columns\TextColumn::make('postal_code')->label('C. Postal'),
                Tables\Columns\IconColumn::make('is_default')->boolean()->label('Por Defecto'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
