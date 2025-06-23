<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class ToyType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles del Juguete')->schema([
                TextInput::make('brand')->label(__('item.field_brand')),
                TextInput::make('material')->label(__('item.field_material')),
                TextInput::make('year')->label(__('item.field_year'))->numeric(),
            ])->columns(3),
        ];
    }
}
