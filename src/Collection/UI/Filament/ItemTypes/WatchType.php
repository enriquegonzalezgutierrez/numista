<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class WatchType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles del Reloj')
                ->schema([
                    TextInput::make('brand')->label(__('item.field_brand')),
                    TextInput::make('model')->label(__('item.field_model')),
                    TextInput::make('material')->label(__('item.field_material')),
                ])->columns(3)
        ];
    }
}
