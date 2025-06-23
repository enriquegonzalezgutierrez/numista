<?php

// app/Filament/ItemTypes/CoinType.php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class CoinType implements ItemType
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

            Section::make(__('item.section_coin'))
                ->schema([
                    TextInput::make('mint_mark')->label(__('item.field_mint_mark')),
                    TextInput::make('composition')->label(__('item.field_composition')),
                    TextInput::make('weight')->label(__('item.field_weight'))->numeric()->suffix('g'),
                ])
                ->columns(3),
        ];
    }
}
