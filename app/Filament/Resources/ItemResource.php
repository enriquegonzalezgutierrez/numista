<?php

namespace App\Filament\Resources;

use App\Filament\ItemStatusManager;
use App\Filament\ItemTypeManager;
use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->maxLength(255),

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
                            ->label(__('item.field_description'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // --- Section 2: Dynamic Type-Specific Fields ---
                // This placeholder will be dynamically filled based on the selected type.
                Group::make()
                    ->schema(function (Get $get): array {
                        $typeKey = $get('type');
                        $manager = new ItemTypeManager();
                        return $manager->getFormComponentsForType($typeKey);
                    })
                    ->columnSpanFull(),

                // --- Section 3: Acquisition and Status (Always Visible) ---
                Forms\Components\Section::make(__('item.section_acquisition'))
                    ->schema([
                        TextInput::make('grade')->label(__('item.field_grade')),
                        TextInput::make('quantity')->label(__('item.field_quantity'))->required()->numeric()->default(1),
                        TextInput::make('purchase_price')->label(__('item.field_purchase_price'))->numeric()->prefix('€'),
                        DatePicker::make('purchase_date')->label(__('item.field_purchase_date')),
                        Select::make('status')
                            ->label(__('item.field_status'))
                            ->options(fn(ItemStatusManager $manager) => $manager->getStatusesForSelect())
                            ->default('in_collection')
                            ->required()
                            ->live(),
                        TextInput::make('sale_price')
                            ->label(__('item.field_sale_price'))
                            ->numeric()
                            ->prefix('€')
                            ->hidden(fn(Get $get): bool => $get('status') !== 'en_venta'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('item.field_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('item.field_type'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __("item.type_{$state}"))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('item.field_status'))
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        // Instantiate the manager and get the translated value
                        $manager = new ItemStatusManager();
                        return $manager->getTranslatedStatus($state);
                    })
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label(__('item.field_quantity'))
                    ->sortable()
                    ->alignEnd(),
            ])
            ->filters([
                // ...
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
