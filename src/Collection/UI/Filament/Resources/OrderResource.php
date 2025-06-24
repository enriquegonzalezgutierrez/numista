<?php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\UI\Filament\Resources\OrderResource\Pages;
use Numista\Collection\UI\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;

class OrderResource extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    protected static ?string $model = Order::class;

    /**
     * The relationship name on the model that links to the tenant.
     */
    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    /**
     * The icon used for the resource's navigation item.
     */
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    /**
     * The sort order for the resource's navigation item.
     */
    protected static ?int $navigationSort = 4;

    /**
     * Get the navigation group for the resource.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('panel.nav_group_shop');
    }

    /**
     * Get the navigation label for the resource.
     */
    public static function getNavigationLabel(): string
    {
        return __('panel.nav_orders');
    }

    /**
     * Get the singular model label for the resource.
     */
    public static function getModelLabel(): string
    {
        return __('panel.label_order');
    }

    /**
     * Get the plural model label for the resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('panel.label_orders');
    }

    /**
     * Disable creating new records from the panel.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Disable editing records from the panel.
     */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /**
     * Defines the resource form schema.
     * This is still needed for the ViewAction.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('order_number')
                        ->label(__('panel.field_order_number'))
                        ->disabled(),

                    TextInput::make('customer_name')
                        ->label(__('panel.field_customer'))
                        ->disabled()
                        ->formatStateUsing(fn (?Model $record) => $record?->customer?->name),

                    TextInput::make('total_amount')
                        ->label(__('panel.field_total_amount'))
                        ->prefix('€')
                        ->disabled(),

                    TextInput::make('status')
                        ->label(__('item.field_status'))
                        ->formatStateUsing(fn ($state) => $state ? __("item.status_{$state}") : '')
                        ->disabled(),

                    TextInput::make('created_at')
                        ->label(__('panel.field_order_date'))
                        ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-')
                        ->disabled(),

                    TextInput::make('updated_at')
                        ->label(__('panel.field_last_update'))
                        ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-')
                        ->disabled(),
                ]),
                // Add other details if needed, e.g., shipping address
                Textarea::make('shipping_address')
                    ->label('Dirección de Envío')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Defines the resource table schema.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->label(__('panel.field_order_number'))->searchable()->sortable(),

                TextColumn::make('customer.name')
                    ->label(__('panel.field_customer'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')->label(__('panel.field_total_amount'))->money('eur')->sortable(),

                TextColumn::make('status')->label(__('item.field_status'))->badge()->formatStateUsing(fn ($state) => __("item.status_{$state}")),

                TextColumn::make('created_at')->label(__('panel.field_order_date'))->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Get the relation managers for the resource.
     */
    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    /**
     * Get the pages for the resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
