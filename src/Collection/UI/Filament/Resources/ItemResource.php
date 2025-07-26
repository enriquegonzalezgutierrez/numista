<?php

// src/Collection/UI/Filament/Resources/ItemResource.php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Attribute;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\UI\Filament\ItemStatusManager;
use Numista\Collection\UI\Filament\ItemTypeManager;
use Numista\Collection\UI\Filament\Resources\ItemResource\Pages;
use Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('item.section_core'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('item.field_name'))
                                    ->required()->maxLength(255)->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label(__('panel.field_slug'))
                                    ->required()->unique(Item::class, 'slug', ignoreRecord: true)
                                    ->disabled()->dehydrated(),

                                Select::make('type')
                                    ->label(__('item.field_type'))
                                    ->options(fn (ItemTypeManager $manager): array => $manager->getTypesForSelect())
                                    ->required()->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('attributes', [])),

                                Textarea::make('description')
                                    ->label(__('panel.field_description'))
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make(__('panel.label_attributes'))
                            ->schema(function (Get $get): array {
                                $itemType = $get('type');
                                if (empty($itemType)) {
                                    return [
                                        Forms\Components\Placeholder::make('no_attributes')
                                            ->label('Select an item type to see its attributes.'),
                                    ];
                                }

                                $attributes = Attribute::query()
                                    ->whereIn('id', function ($query) use ($itemType) {
                                        $query->select('attribute_id')
                                            ->from('attribute_item_type')
                                            ->where('item_type', $itemType);
                                    })
                                    ->orderBy('name')
                                    ->get();

                                if ($attributes->isEmpty()) {
                                    return [];
                                }

                                return $attributes->map(function (Attribute $attribute) {
                                    if ($attribute->type === 'select') {
                                        $options = $attribute->values->pluck('value', 'id');
                                        if (strtolower($attribute->name) === 'grade') {
                                            $options = $options->mapWithKeys(fn ($value, $id) => [$id => __("item.options.grade.{$value}")]);
                                        }
                                        $field = Select::make("attributes.{$attribute->id}.attribute_value_id")->options($options);
                                    } else {
                                        $field = match ($attribute->type) {
                                            'number' => TextInput::make("attributes.{$attribute->id}.value")->numeric(),
                                            'date' => DatePicker::make("attributes.{$attribute->id}.value"),
                                            default => TextInput::make("attributes.{$attribute->id}.value"),
                                        };
                                    }

                                    // THE FIX: Use the correct translation key from 'panel.php'
                                    $key = 'panel.attribute_name_'.strtolower(str_replace(' ', '_', $attribute->name));

                                    return $field->label(trans()->has($key) ? __($key) : $attribute->name);
                                })->all();
                            })->columns(3),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('item.section_acquisition'))
                            ->schema([
                                TextInput::make('quantity')->required()->numeric()->default(1)->label(__('item.field_quantity')),
                                TextInput::make('purchase_price')->numeric()->prefix('€')->label(__('item.field_purchase_price')),
                                DatePicker::make('purchase_date')->label(__('item.field_purchase_date')),
                                Select::make('status')->options(fn (ItemStatusManager $manager) => $manager->getStatusesForSelect())->default('in_collection')->required()->live()->label(__('item.field_status')),
                                TextInput::make('sale_price')->numeric()->prefix('€')->visible(fn (Get $get): bool => $get('status') === 'for_sale')->label(__('item.field_sale_price')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder(__('panel.search_placeholder'))
            ->columns([
                ImageColumn::make('images.path')->label(__('panel.field_image_preview'))->disk('tenants')->circular()->stacked()->limit(1)->defaultImageUrl(url('/images/placeholder.svg')),
                TextColumn::make('name')->label(__('item.field_name'))->searchable()->sortable(),
                TextColumn::make('type')->label(__('item.field_type'))->badge()->formatStateUsing(fn (string $state): string => __("item.type_{$state}"))->searchable()->sortable(),
                TextColumn::make('collections.name')->label(__('panel.label_collections'))->badge()->searchable(),
                TextColumn::make('status')->label(__('item.field_status'))->badge()->formatStateUsing(fn (string $state): string => __("item.status_{$state}"))->searchable(),
                TextColumn::make('quantity')->label(__('item.field_quantity'))->sortable()->alignEnd(),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('panel.field_type'))->options(fn (ItemTypeManager $manager) => $manager->getTypesForSelect()),
                SelectFilter::make('status')->label(__('panel.field_status'))->options(fn (ItemStatusManager $manager) => $manager->getStatusesForSelect()),
                SelectFilter::make('categories')->label(__('panel.label_categories'))->relationship('categories', 'name')->searchable()->preload()->multiple(),
                SelectFilter::make('collections')->label(__('panel.filter_collection'))->relationship('collections', 'name')->searchable()->preload()->multiple(),
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
            RelationManagers\CollectionsRelationManager::class,
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\ImagesRelationManager::class,
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
