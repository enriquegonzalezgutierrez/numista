<?php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class BookType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make('Detalles del Libro')
                ->schema([
                    TextInput::make('author')->label(__('item.field_author')),
                    TextInput::make('publisher')->label(__('item.field_publisher')),
                    TextInput::make('isbn')->label(__('item.field_isbn')),
                ])->columns(3)
        ];
    }
}
