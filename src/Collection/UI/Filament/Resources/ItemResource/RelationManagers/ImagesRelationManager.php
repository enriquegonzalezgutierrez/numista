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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        // This title is specific to the item context, so using 'item.php' is correct.
        return __('item.section_images');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('path')
                    ->label(__('panel.label_image_files')) // More specific label: "Archivo de Imagen"
                    ->required()
                    ->disk('tenants')
                    ->directory('tenant-' . Filament::getTenant()?->id . '/item-images')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),

                Textarea::make('alt_text')
                    ->label(__('panel.label_alt_text')) // Using the generic label from panel.php
                    ->helperText(__('panel.helper_alt_text')) // Using the generic helper from panel.php
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
                    ->label(__('panel.label_image_preview')) // Using generic label from panel.php
                    ->disk('tenants'),

                TextColumn::make('alt_text')
                    ->label(__('panel.label_alt_text')), // Using generic label from panel.php
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('panel.action_create')) // More specific: "Añadir Imagen"
                    ->modalHeading(__('panel.modal_create_image_title')),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('panel.action_edit')), // Generic "Editar" is fine here
                DeleteAction::make()
                    ->label(__('panel.action_delete')), // Generic "Eliminar" is fine here
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
