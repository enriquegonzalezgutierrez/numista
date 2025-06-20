<?php

namespace Numista\Collection\UI\Filament\Resources;

use Numista\Collection\Domain\Models\Category;
use Numista\Collection\UI\Filament\Resources\CategoryResource\Pages;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $tenantOwnershipRelationshipName = 'tenant';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?int $navigationSort = 2;

    // --- Using Translation Keys for Labels ---
    public static function getNavigationLabel(): string
    {
        return __('panel.category_nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('panel.category_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.category_plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('panel.field_category_name'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->label(__('panel.field_slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Select::make('parent_id')
                    ->label(__('panel.field_parent_category'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->placeholder(__('panel.placeholder_none')),
                Toggle::make('is_visible')
                    ->label(__('panel.field_is_visible')),
                Textarea::make('description')
                    ->label(__('panel.field_description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('panel.field_category_name'))->searchable()->sortable(),
                TextColumn::make('parent.name')->label(__('panel.field_parent_category'))->searchable()->sortable()->placeholder(__('panel.placeholder_none')),
                IconColumn::make('is_visible')->label(__('panel.field_is_visible'))->boolean(),
                TextColumn::make('items_count')->counts('items')->label(__('panel.field_items_count')),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([/* ... */])]);
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
