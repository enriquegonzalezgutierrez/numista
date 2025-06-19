<?php
// app/Filament/ItemTypes/PenType.php
namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class PenType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de Estilográfica / Bolígrafo')
                ->schema([
                    TextInput::make('brand')->label(__('item.field_brand')),
                    TextInput::make('model')->label(__('item.field_model')),
                    TextInput::make('material')->label(__('item.field_material'))->placeholder('Ej: Resina, Celuloide'),
                ])->columns(3)
        ];
    }
}
