<?php

namespace App\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class StampType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles del Sello')
                ->schema([
                    Select::make('country_id')->label(__('item.field_country'))->relationship('country', 'name')->searchable()->preload(),
                    TextInput::make('year')->label(__('item.field_year'))->numeric(),
                    TextInput::make('face_value')->label(__('item.field_face_value')),
                ])->columns(3)
        ];
    }
}
