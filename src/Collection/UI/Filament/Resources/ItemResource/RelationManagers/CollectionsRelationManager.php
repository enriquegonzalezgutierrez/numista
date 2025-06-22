<?php

namespace Numista\Collection\UI\Filament\Resources\ItemResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CollectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'collections';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.label_collections');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('panel.field_collection_name')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label(__('panel.action_attach'))
                    ->modalHeading(__('panel.modal_attach_title_collection'))
                    ->modalSubmitActionLabel(__('panel.modal_attach_button'))
                    ->preloadRecordSelect(),
            ])
            ->actions([
                DetachAction::make()
                    ->label(__('panel.action_detach')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
