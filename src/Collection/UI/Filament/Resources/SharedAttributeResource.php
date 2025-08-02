<?php

// src/Collection/UI/Filament/Resources/SharedAttributeResource.php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Numista\Collection\Domain\Models\ItemType;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\UI\Filament\Resources\SharedAttributeResource\Pages;

class SharedAttributeResource extends Resource
{
    protected static ?string $model = SharedAttribute::class;

    protected static bool $isScopedToTenant = false;

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
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('panel.field_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label(__('panel.field_type'))
                            ->options([
                                'text' => __('panel.attribute_type_text'),
                                'number' => __('panel.attribute_type_number'),
                                'date' => __('panel.attribute_type_date'),
                                'select' => __('panel.attribute_type_select'),
                            ])
                            ->live()
                            ->required(),
                        Forms\Components\Toggle::make('is_filterable')
                            ->label(__('panel.field_is_filterable'))
                            ->helperText(__('panel.helper_is_filterable')),
                        Forms\Components\CheckboxList::make('itemTypes')
                            ->label(__('panel.label_applicable_item_types'))
                            ->relationship(name: 'itemTypes', titleAttribute: 'name')
                            ->options(function (): array {
                                $types = ItemType::all();
                                $translatedOptions = $types->mapWithKeys(function (ItemType $type) {
                                    return [$type->id => __('item.type_'.$type->name)];
                                })->toArray();
                                asort($translatedOptions);

                                return $translatedOptions;
                            })
                            ->columns(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                // THE FIX: We use a Repeater field inside the form schema itself.
                // This is the standard, reliable way to handle one-to-many relationships in Filament forms.
                Forms\Components\Section::make(__('panel.label_predefined_options'))
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('value')
                                    ->label(__('panel.field_value'))
                                    ->required(),
                            ])
                            // THE FIX: Apply translations to the Repeater component itself.
                            ->label(__('panel.label_options')) // Sets the label above the repeater items.
                            ->addActionLabel(__('panel.action_create_option')) // Sets the text for the "Add" button.
                            ->columns(1),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'select'), // It will be reactive!
            ]);
    }

    public static function getRelations(): array
    {
        // THE FIX: We removed the relation manager, so this array is now empty.
        return [];
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSharedAttributes::route('/'),
            'create' => Pages\CreateSharedAttribute::route('/create'),
            'edit' => Pages\EditSharedAttribute::route('/{record}/edit'),
        ];
    }
}
