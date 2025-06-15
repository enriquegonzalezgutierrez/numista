<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->columnSpan('full'),

            Select::make('type')
                ->options([
                    'coin' => 'Coin',
                    'banknote' => 'Banknote',
                    'comic' => 'Comic',
                    'medal' => 'Medal',
                ])
                ->required(),

            TextInput::make('quantity')
                ->required()
                ->numeric()
                ->default(1),

            TextInput::make('description')
                ->columnSpan('full'),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('type')
                ->badge()
                ->searchable()
                ->sortable(),

            TextColumn::make('status')
                ->badge()
                ->searchable(),

            TextColumn::make('quantity')
                ->sortable()
                ->alignEnd(),

            TextColumn::make('created_at')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
