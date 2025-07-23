<?php

// src/Collection/UI/Filament/Resources/AttributeResource.php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\UI\Filament\Resources\AttributeResource\Pages;
use Numista\Collection\UI\Filament\Resources\AttributeResource\RelationManagers;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string
    {
        return __('panel.nav_group_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('panel.nav_attributes');
    }

    public static function getModelLabel(): string
    {
        return __('panel.label_attribute');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.label_attributes');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('panel.field_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label(__('panel.field_type'))
                    ->options([
                        'text' => 'Texto',
                        'number' => 'Número',
                        'date' => 'Fecha',
                        'select' => 'Opciones Seleccionables',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_filterable')
                    ->label(__('panel.field_is_filterable'))
                    ->helperText(__('panel.helper_is_filterable')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('panel.field_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('panel.field_type'))
                    ->badge(),
                Tables\Columns\IconColumn::make('is_filterable')
                    ->label(__('panel.field_is_filterable'))
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\ValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
