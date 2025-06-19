<?php
// app/Filament/ItemTypes/JewelryType.php
namespace App\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class JewelryType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de la Joya')
                ->schema([
                    TextInput::make('material')->label(__('item.field_material'))->placeholder('Ej: Oro 18k, Plata 925'),
                    TextInput::make('gemstone')->label(__('item.field_gemstone'))->placeholder('Ej: Diamante, Rubí'),
                    TextInput::make('weight')->label(__('item.field_weight'))->numeric()->suffix('g'),
                ])->columns(3)
        ];
    }
}
