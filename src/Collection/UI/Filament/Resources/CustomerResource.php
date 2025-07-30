<?php

// src/Collection/UI/Filament/Resources/CustomerResource.php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\UI\Filament\Resources\CustomerResource\Pages;
use Numista\Collection\UI\Filament\Resources\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('panel.nav_group_shop');
    }

    public static function getNavigationLabel(): string
    {
        return __('panel.nav_customers');
    }

    public static function getModelLabel(): string
    {
        return __('panel.label_customer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.label_customers');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('panel.section_user_information'))
                    ->schema([
                        Forms\Components\TextInput::make('user.name')->label(__('panel.field_name')),
                        Forms\Components\TextInput::make('user.email')->label(__('panel.field_email')),
                    ])->columns(2),

                Forms\Components\Section::make(__('panel.section_contact_information'))
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')->label(__('panel.field_phone_number')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->whereHas('orders', function (Builder $query) {
                        $query->where('tenant_id', Filament::getTenant()->id);
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label(__('panel.field_name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.email')->label(__('panel.field_email'))->searchable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts(['orders' => fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)])
                    ->label(__('panel.field_orders_count'))
                    ->url(fn (Customer $record): string => OrderResource::getUrl('index', ['tableFilters[customer][value]' => $record->user_id, 'tenant' => Filament::getTenant()]))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('panel.field_registration_date'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([Tables\Actions\ViewAction::make()])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AddressesRelationManager::class,
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }
}
