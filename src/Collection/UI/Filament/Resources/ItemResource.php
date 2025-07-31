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
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\ItemType;
use Numista\Collection\Domain\Models\SharedAttribute;
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
                                TextInput::make('name')->label(__('item.field_name'))->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')->label(__('panel.field_slug'))->required()->unique(Item::class, 'slug', ignoreRecord: true)->disabled()->dehydrated(),
                                Select::make('type')
                                    ->label(__('item.field_type'))
                                    ->options(fn (ItemTypeManager $manager): array => $manager->getTypesForSelect())
                                    ->required()
                                    ->live()
                                    // THE FIX: Do not reset the 'attributes' state when the type changes.
                                    // This preserves the data if the user switches back and forth.
                                    ->afterStateUpdated(fn (Set $set) => $set('attributes', $form->getRawState()['attributes'] ?? [])),
                                Textarea::make('description')->label(__('panel.field_description'))->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make(__('panel.label_attributes'))
                            ->schema(function (Get $get): array {
                                $itemTypeName = $get('type');
                                if (empty($itemTypeName)) {
                                    return [];
                                }

                                // Find the ItemType model by its name
                                $itemType = ItemType::where('name', $itemTypeName)->first();
                                if (! $itemType) {
                                    return [];
                                }

                                // THE FIX: This is the correct query. It joins on the pivot table
                                // and filters by the correct `item_type_id`.
                                $attributes = SharedAttribute::query()
                                    ->whereHas('itemTypes', function ($query) use ($itemType) {
                                        $query->where('item_type_id', $itemType->id);
                                    })
                                    ->with('options')
                                    ->orderBy('name')
                                    ->get();

                                if ($attributes->isEmpty()) {
                                    return [];
                                }

                                return $attributes->map(function (SharedAttribute $attribute) {
                                    $field = match ($attribute->type) {
                                        'select' => Forms\Components\Select::make("attributes.{$attribute->id}.attribute_option_id")
                                            ->options($attribute->options->pluck('value', 'id')),
                                        'number' => Forms\Components\TextInput::make("attributes.{$attribute->id}.value")->numeric(),
                                        'date' => Forms\Components\DatePicker::make("attributes.{$attribute->id}.value"),
                                        default => Forms\Components\TextInput::make("attributes.{$attribute->id}.value"),
                                    };

                                    $key = 'panel.attribute_name_'.strtolower(str_replace(' ', '_', $attribute->name));

                                    return $field->label(trans()->has($key) ? __($key) : $attribute->name);
                                })->all();
                            })->columns(3),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('item.section_acquisition'))
                            ->schema([
                                TextInput::make('quantity')->required()->numeric()->default(1)->label(__('item.field_quantity')),
                                TextInput::make('purchase_price')->numeric()->prefix('€')->label(__('item.field_purchase_price')),
                                DatePicker::make('purchase_date')->label(__('item.field_purchase_date')),
                                Select::make('status')->options(fn (ItemStatusManager $manager) => $manager->getStatusesForSelect())->default('in_collection')->required()->live()->label(__('item.field_status')),
                                TextInput::make('sale_price')->numeric()->prefix('€')->visible(fn (Get $get): bool => in_array($get('status'), ['for_sale', 'sold']))->label(__('item.field_sale_price')),
                            ]),
                    ])->columnSpan(['lg' => 1]),
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
                    BulkAction::make('change_status')
                        ->label(__('panel.action_bulk_change_status'))
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->form([
                            Select::make('status')
                                ->label(__('panel.field_new_status'))
                                ->options(fn (ItemStatusManager $manager): array => $manager->getStatusesForSelect())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update(['status' => $data['status']]);
                        })
                        ->successNotificationTitle(__('panel.notification_status_updated'))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\CollectionsRelationManager::class,
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
