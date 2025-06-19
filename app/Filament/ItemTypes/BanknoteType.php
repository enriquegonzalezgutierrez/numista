<?php
// app/Filament/ItemTypes/BanknoteType.php

namespace App\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class BanknoteType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make(__('item.section_numismatic'))
                ->schema([
                    Select::make('country_id')->label(__('item.field_country'))->relationship('country', 'name')->searchable()->preload(),
                    TextInput::make('year')->label(__('item.field_year'))->numeric(),
                    TextInput::make('denomination')->label(__('item.field_denomination')),
                ])
                ->columns(3),

            Section::make(__('item.section_banknote'))
                ->schema([
                    TextInput::make('serial_number')->label(__('item.field_serial_number')),
                ]),
        ];
    }
}
