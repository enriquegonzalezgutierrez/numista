<?php

// src/Collection/UI/Filament/Resources/OrderResource.php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\UI\Filament\Resources\OrderResource\Pages;
use Numista\Collection\UI\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('panel.nav_group_shop');
    }

    public static function getNavigationLabel(): string
    {
        return __('panel.nav_orders');
    }

    public static function getModelLabel(): string
    {
        return __('panel.label_order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.label_orders');
    }

    public static function canCreate(): bool
    {
        // Keep creation disabled as orders should come from the public side
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        // Keep the main 'Edit' page disabled, as we will use a modal Action
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('order_number')->label(__('panel.field_order_number'))->disabled(),
                    TextInput::make('customer.name')->label(__('panel.field_customer'))->disabled(),
                    TextInput::make('total_amount')->label(__('panel.field_total_amount'))->prefix('€')->disabled(),
                    TextInput::make('status')->label(__('item.field_status'))->formatStateUsing(fn ($state) => $state ? __("item.status_{$state}") : '')->disabled(),
                    TextInput::make('created_at')->label(__('panel.field_order_date'))->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-')->disabled(),
                    TextInput::make('updated_at')->label(__('panel.field_last_update'))->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-')->disabled(),
                ]),
                Textarea::make('shipping_address')->label('Dirección de Envío')->disabled()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->label(__('panel.field_order_number'))->searchable()->sortable(),
                TextColumn::make('customer.name')->label(__('panel.field_customer'))->searchable()->sortable(),
                TextColumn::make('total_amount')->label(__('panel.field_total_amount'))->money('eur')->sortable(),
                TextColumn::make('status')
                    ->label(__('item.field_status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'primary',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => __("item.status_{$state}")),
                TextColumn::make('created_at')->label(__('panel.field_order_date'))->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('update_status')
                    ->label(__('panel.action_update_status')) // Use translation key
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading(__('panel.modal_change_order_status_heading')) // Use translation key
                    ->form([
                        Select::make('status')
                            ->label(__('panel.field_new_status')) // Use translation key
                            ->options([
                                'pending' => __('item.status_pending'),
                                'paid' => __('item.status_paid'),
                                'shipped' => __('item.status_shipped'),
                                'completed' => __('item.status_completed'),
                                'cancelled' => __('item.status_cancelled'),
                            ])
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                        // THE FIX FOR THE NOTIFICATION: Send it explicitly inside the action closure.
                        Notification::make()
                            ->success()
                            ->title(__('panel.notification_order_status_updated_title')) // Use translation key
                            ->body(__('panel.notification_order_status_updated_body')) // Use translation key
                            ->send();
                    }),
                // We remove successNotification() from here as we are sending it manually.
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
