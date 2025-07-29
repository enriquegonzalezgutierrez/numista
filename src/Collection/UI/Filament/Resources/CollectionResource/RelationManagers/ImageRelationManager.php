<?php

namespace Numista\Collection\UI\Filament\Resources\CollectionResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Numista\Collection\Domain\Models\Image;

class ImageRelationManager extends RelationManager
{
    protected static string $relationship = 'image';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('path')
                    ->label(__('panel.field_image_file'))
                    ->required()
                    ->disk('tenants')
                    ->directory('tenant-'.Filament::getTenant()?->id.'/collection-images')
                    ->image()
                    ->imageEditor(),
                Forms\Components\Textarea::make('alt_text')
                    ->label(__('panel.field_alt_text'))
                    ->helperText(__('panel.helper_alt_text')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label(__('panel.field_image_preview'))
                    ->disk('tenants')
                    // THE FIX: Use the new accessor for the URL
                    ->url(fn (Image $record): string => $record->url)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('alt_text')
                    ->label(__('panel.field_alt_text')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Subir Imagen')
                    ->modalHeading('Subir Nueva Imagen de Colección'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public function canCreate(): bool
    {
        return $this->ownerRecord->image()->count() === 0;
    }
}
