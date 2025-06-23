<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class SportsCollectibleType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles Deportivos')->schema([
                TextInput::make('sport')->label(__('item.field_sport')),
                TextInput::make('team')->label(__('item.field_team')),
                TextInput::make('player')->label(__('item.field_player')),
                TextInput::make('event')->label(__('item.field_event')),
            ])->columns(2),
        ];
    }
}
