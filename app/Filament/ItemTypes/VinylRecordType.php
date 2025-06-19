<?php
// app/Filament/ItemTypes/VinylRecordType.php
namespace App\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class VinylRecordType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles del Disco')
                ->schema([
                    TextInput::make('artist')->label(__('item.field_artist')),
                    TextInput::make('record_label')->label(__('item.field_record_label')),
                    TextInput::make('year')->label(__('item.field_year'))->numeric(),
                ])->columns(3)
        ];
    }
}
