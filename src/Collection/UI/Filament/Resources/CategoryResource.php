<?php

namespace Numista\Collection\UI\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\UI\Filament\Resources\CategoryResource\Pages;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 2;

    /**
     * Defines the navigation label for this resource.
     */
    public static function getNavigationLabel(): string
    {
        return __('panel.nav_categories');
    }

    /**
     * Defines the singular model label for this resource.
     */
    public static function getModelLabel(): string
    {
        return __('panel.label_category');
    }

    /**
     * Defines the plural model label for this resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('panel.label_categories');
    }

    /**
     * Defines the resource form schema.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('panel.field_name'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->label(__('panel.field_slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                Select::make('parent_id')
                    ->label(__('panel.field_parent_category'))
                    ->searchable()
                    ->placeholder(__('panel.placeholder_none'))
                    ->options(function (?Category $record): array {
                        $query = Category::query();

                        if ($record) {
                            $query->where('id', '!=', $record->id);

                            $descendantIds = $record->descendants->pluck('id')->toArray();

                            if (! empty($descendantIds)) {
                                $query->whereNotIn('id', $descendantIds);
                            }
                        }

                        return $query->pluck('name', 'id')->toArray();
                    }),

                Toggle::make('is_visible')
                    ->label(__('panel.field_is_visible')),

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
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('panel.field_name'))
                    ->searchable()
                    ->sortable()
                    ->description(function (Category $record): string {
                        $record->load('ancestors');

                        return $record->ancestors->pluck('name')->push($record->name)->implode(' > ');
                    }),

                TextColumn::make('parent.name')
                    ->label(__('panel.field_parent_category'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('panel.placeholder_none')),

                IconColumn::make('is_visible')
                    ->label(__('panel.field_is_visible'))
                    ->boolean(),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label(__('panel.field_items_count')),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('change_visibility')
                        ->label(__('panel.action_bulk_change_visibility'))
                        ->icon('heroicon-o-eye')
                        ->requiresConfirmation()
                        ->form([
                            Toggle::make('is_visible')
                                ->label(__('panel.field_visibility'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update(['is_visible' => $data['is_visible']]);
                        })
                        ->successNotificationTitle(__('panel.notification_visibility_updated'))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
