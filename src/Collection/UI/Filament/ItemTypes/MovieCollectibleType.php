<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class MovieCollectibleType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de Cine')->schema([
                TextInput::make('movie_title')->label(__('item.field_movie_title')),
                TextInput::make('character')->label(__('item.field_character')),
                TextInput::make('year')->label(__('item.field_year'))->numeric(),
            ])->columns(3),
        ];
    }
}
