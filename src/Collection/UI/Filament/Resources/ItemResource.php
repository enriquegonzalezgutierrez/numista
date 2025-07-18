<?php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\UI\Filament\ItemGradeManager;
use Numista\Collection\UI\Filament\ItemStatusManager;
use Numista\Collection\UI\Filament\ItemTypeManager;
use Numista\Collection\UI\Filament\Resources\ItemResource\Pages;
use Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers\CategoriesRelationManager;
use Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers\CollectionsRelationManager;
use Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers\ImagesRelationManager;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    /**
     * Defines the navigation label for this resource.
     */
    public static function getNavigationLabel(): string
    {
        return __('panel.nav_items');
    }

    public static function getModelLabel(): string
    {
        return __('panel.label_item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.label_items');
    }

    /**
     * Defines the resource form schema.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- Section 1: Core and Common Fields ---
                Forms\Components\Section::make(__('item.section_core'))
                    ->schema([
                        // Item Name field
                        TextInput::make('name')
                            ->label(__('item.field_name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true) // Trigger update when user leaves the field
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))), // Generate slug on the fly

                        // --- NEW: Slug Field ---
                        TextInput::make('slug')
                            ->label(__('panel.field_slug'))
                            ->required()
                            ->unique(Item::class, 'slug', ignoreRecord: true)
                            ->disabled() // User cannot edit it directly
                            ->dehydrated(), // Ensures the value is saved even though it's disabled

                        // Item Type dropdown, now fully dynamic
                        Select::make('type')
                            ->label(__('item.field_type'))
                            ->options(function (ItemTypeManager $manager): array {
                                return $manager->getTypesForSelect();
                            })
                            ->required()
                            ->live(), // Important for reactivity

                        // Description field, spans the full width
                        Textarea::make('description')
                            ->label(__('panel.field_description'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // --- Section 2: Dynamic Type-Specific Fields ---
                // This placeholder will be dynamically filled based on the selected type.
                Group::make()
                    ->schema(function (Get $get): array {
                        $typeKey = $get('type');
                        $manager = new ItemTypeManager;

                        return $manager->getFormComponentsForType($typeKey);
                    })
                    ->columnSpanFull(),

                // --- Section 3: Acquisition and Status (Always Visible) ---
                Forms\Components\Section::make(__('item.section_acquisition'))
                    ->schema([
                        Select::make('grade')
                            ->label(__('item.field_grade'))
                            ->options(fn (ItemGradeManager $manager) => $manager->getGradesForSelect())
                            ->searchable(),

                        TextInput::make('quantity')
                            ->label(__('item.field_quantity'))
                            ->required()
                            ->numeric()
                            ->default(1),

                        TextInput::make('purchase_price')
                            ->label(__('item.field_purchase_price'))
                            ->numeric()
                            ->prefix('€'),

                        DatePicker::make('purchase_date')
                            ->label(__('item.field_purchase_date')),

                        Select::make('status')
                            ->label(__('item.field_status'))
                            ->options(fn (ItemStatusManager $manager) => $manager->getStatusesForSelect())
                            ->default('in_collection')
                            ->required()
                            ->live(),

                        TextInput::make('sale_price')
                            ->label(__('item.field_sale_price'))
                            ->numeric()
                            ->prefix('€')
                            ->hidden(fn (Get $get): bool => $get('status') !== 'for_sale'),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Defines the resource table schema.
     */
    public static function table(Table $table): Table
    {
        return $table
            // Use translation key for the search placeholder
            ->searchPlaceholder(__('panel.search_placeholder'))
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label(__('panel.field_image_preview'))
                    ->disk('tenants')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->state(function (Model $record): ?string {
                        return $record->images->first()?->path;
                    }),

                TextColumn::make('name')
                    ->label(__('item.field_name'))
                    ->searchable() // Search is enabled on this column
                    ->sortable(),

                // Add the description column for searching, but keep it hidden by default
                TextColumn::make('description')
                    ->label(__('panel.field_description'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->label(__('item.field_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("item.type_{$state}"))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('collections.name')
                    ->label(__('panel.label_collections'))
                    ->badge()
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('item.field_status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("item.status_{$state}"))
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label(__('item.field_quantity'))
                    ->sortable()
                    ->alignEnd(),
            ])
            ->filters([
                // --- Filter by Item Type ---
                SelectFilter::make('type')
                    ->label(__('panel.field_type'))
                    ->options(fn (\Numista\Collection\UI\Filament\ItemTypeManager $manager) => $manager->getTypesForSelect()),

                // --- Filter by Item Status ---
                SelectFilter::make('status')
                    ->label(__('panel.field_status'))
                    ->options(fn (\Numista\Collection\UI\Filament\ItemStatusManager $manager) => $manager->getStatusesForSelect()),

                // --- Filter by Category ---
                SelectFilter::make('categories')
                    ->label(__('panel.label_categories'))
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('collections')
                    ->label(__('panel.filter_collection'))
                    ->relationship('collections', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            // In ItemResource.php -> table()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // --- Bulk Action to change status ---
                    BulkAction::make('change_status')
                        ->label(__('panel.action_bulk_change_status'))
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation() // Good practice to avoid accidental clicks
                        ->form([
                            Select::make('status')
                                ->label(__('panel.field_new_status'))
                                ->options(fn (ItemStatusManager $manager) => $manager->getStatusesForSelect())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update(['status' => $data['status']]);
                        })
                        ->successNotificationTitle(__('panel.notification_status_updated')),

                    // --- Bulk Action to attach categories ---
                    BulkAction::make('attach_category')
                        ->label(__('panel.action_bulk_attach_category'))
                        ->icon('heroicon-o-tag')
                        ->form([
                            Select::make('categories')
                                ->label(__('panel.field_select_categories'))
                                ->relationship('categories', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->categories()->syncWithoutDetaching($data['categories']);
                            }
                        })
                        ->successNotificationTitle(__('panel.notification_categories_attached')),

                    // --- Default Delete Bulk Action ---
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CollectionsRelationManager::class,
            CategoriesRelationManager::class,
            ImagesRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('images');
    }
}
