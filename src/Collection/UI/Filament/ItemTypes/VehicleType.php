<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class VehicleType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles del Vehículo')
                ->schema([
                    TextInput::make('brand')->label(__('item.field_brand')),
                    TextInput::make('model')->label(__('item.field_model')),
                    TextInput::make('year')->label(__('item.field_year'))->numeric(),
                    TextInput::make('license_plate')->label(__('item.field_license_plate')),
                    TextInput::make('chassis_number')->label(__('item.field_chassis_number')),
                ])->columns(2)
        ];
    }
}
