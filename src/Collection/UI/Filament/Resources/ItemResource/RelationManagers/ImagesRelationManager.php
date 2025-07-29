<?php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Application\Items\SetFeaturedImageService;
use Numista\Collection\Domain\Models\Image;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.label_images');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('path')
                    ->label(__('panel.field_image_file'))
                    ->required()
                    ->disk('tenants')
                    ->directory('tenant-'.Filament::getTenant()?->id.'/item-images')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),

                Textarea::make('alt_text')
                    ->label(__('panel.field_alt_text'))
                    ->helperText(__('panel.helper_alt_text'))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->reorderable('order_column')
            ->columns([
                ImageColumn::make('path')
                    ->label(__('panel.field_image_preview'))
                    ->disk('tenants')
                    // THE FIX: Use the new accessor for the URL
                    ->url(fn (Image $record): string => $record->url)
                    ->openUrlInNewTab(),

                TextColumn::make('alt_text')
                    ->label(__('panel.field_alt_text')),

                ToggleColumn::make('is_featured')
                    ->label('Destacada')
                    ->afterStateUpdated(function (Model $record, $state, SetFeaturedImageService $service) {
                        $service->handle($record, $state);
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('panel.action_create_image'))
                    ->modalHeading(__('panel.modal_create_image_title')),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('panel.action_edit')),
                DeleteAction::make()
                    ->label(__('panel.action_delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
