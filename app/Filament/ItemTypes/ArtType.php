<?php
// app/Filament/ItemTypes/ArtType.php
namespace App\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class ArtType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles de la Obra de Arte')
                ->schema([
                    TextInput::make('artist')->label(__('item.field_artist')),
                    TextInput::make('dimensions')->label(__('item.field_dimensions'))->placeholder('Ej: 30x40 cm'),
                    TextInput::make('material')->label(__('item.field_material'))->placeholder('Ej: Óleo sobre lienzo'),
                ])->columns(3)
        ];
    }
}
