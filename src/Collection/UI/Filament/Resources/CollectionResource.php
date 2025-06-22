<?php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Numista\Collection\UI\Filament\Resources\CollectionResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Numista\Collection\Domain\Models\Collection;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    /**
     * Defines the navigation label for this resource.
     */
    public static function getNavigationLabel(): string
    {
        return __('panel.nav_collections');
    }

    /**
     * Defines the singular model label for this resource.
     */
    public static function getModelLabel(): string
    {
        return __('panel.label_collection');
    }

    /**
     * Defines the plural model label for this resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('panel.label_collections');
    }

    /**
     * Defines the resource form schema.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label(__('panel.field_collection_name'))
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),

            TextInput::make('slug')
                ->label(__('panel.field_slug'))
                ->required()
                ->unique(ignoreRecord: true),

            Textarea::make('description')
                ->label(__('panel.field_description'))
                ->columnSpanFull(),
        ]);
    }

    /**
     * Defines the resource table schema.
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label(__('panel.field_collection_name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('items_count')
                ->counts('items')
                ->label(__('panel.field_items_count')),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
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
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
