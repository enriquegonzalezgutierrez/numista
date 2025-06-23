<?php

// app/Filament/ItemTypes/CameraType.php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class CameraType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de la Cámara')
                ->schema([
                    TextInput::make('brand')->label(__('item.field_brand')),
                    TextInput::make('model')->label(__('item.field_model')),
                    TextInput::make('year')->label(__('item.field_year'))->numeric(),
                ])->columns(3),
        ];
    }
}
