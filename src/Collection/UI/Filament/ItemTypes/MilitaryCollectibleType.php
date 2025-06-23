<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class MilitaryCollectibleType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles Militares')->schema([
                Select::make('country_id')->label(__('item.field_country'))->relationship('country', 'name')->searchable()->preload(),
                TextInput::make('conflict')->label(__('item.field_conflict'))->placeholder('Ej: Segunda Guerra Mundial'),
                TextInput::make('year')->label(__('item.field_year'))->numeric(),
            ])->columns(3),
        ];
    }
}
