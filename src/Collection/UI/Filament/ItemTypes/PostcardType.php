<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class PostcardType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de la Postal')->schema([
                TextInput::make('publisher_postcard')->label(__('item.field_publisher_postcard')),
                TextInput::make('origin_location')->label(__('item.field_origin_location')),
                TextInput::make('year')->label(__('item.field_year'))->numeric(),
            ])->columns(3),
        ];
    }
}
