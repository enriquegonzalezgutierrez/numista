<?php

namespace App\Filament\ItemTypes;

use Filament\Forms\Components\{Section, TextInput};

class PhotoType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de la Fotografía')->schema([
                TextInput::make('photographer')->label(__('item.field_photographer')),
                TextInput::make('location')->label(__('item.field_location')),
                TextInput::make('technique')->label(__('item.field_technique'))->placeholder('Ej: Daguerrotipo, Albúmina'),
                TextInput::make('year')->label(__('item.field_year'))->numeric(),
            ])->columns(2)
        ];
    }
}
