<?php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('item.section_images');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('path')
                    ->label(__('item.field_images'))
                    ->required()
                    ->disk('tenants')
                    ->directory('tenant-' . Filament::getTenant()?->id . '/item-images')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),

                Textarea::make('alt_text')
                    ->label(__('item.field_alt_text'))
                    ->helperText(__('item.helper_alt_text'))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->reorderable('order_column') // Enable reordering
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('tenants'), // Tell the column to use our custom disk

                Tables\Columns\TextColumn::make('alt_text')
                    ->label('Alternative Text'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('item.button_create'))
                    ->modalHeading(__('item.modal_create_image_title')),
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
}
