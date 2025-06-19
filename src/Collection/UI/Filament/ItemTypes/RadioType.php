<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\{Section, TextInput};

class RadioType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de Radio / Gramófono')->schema([
                TextInput::make('brand')->label(__('item.field_brand')),
                TextInput::make('model')->label(__('item.field_model')),
                TextInput::make('year')->label(__('item.field_year'))->numeric(),
            ])->columns(3)
        ];
    }
}
