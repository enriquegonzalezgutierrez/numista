<?php

// src/Collection/UI/Filament/Resources/AttributeResource/RelationManagers/ValuesRelationManager.php

namespace Numista\Collection\UI\Filament\Resources\AttributeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Numista\Collection\Domain\Models\Attribute as AttributeModel;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('panel.label_predefined_options');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('value')
                    ->label(__('panel.field_value'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('value')
                    ->label(__('panel.field_value')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('panel.action_create_option')),
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

    /**
     * THE FIX: This method makes the relation manager only visible
     * if the parent Attribute's type is 'select'.
     */
    public static function isVisibleFor(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof AttributeModel) {
            return false;
        }

        return $ownerRecord->type === 'select';
    }
}
