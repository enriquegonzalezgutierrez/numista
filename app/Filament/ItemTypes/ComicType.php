<?php
// app/Filament/ItemTypes/ComicType.php

namespace App\Filament\ItemTypes;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class ComicType implements ItemType
{
    public static function getFormComponents(): array
    {
        return [
            Section::make(__('item.section_comic'))
                ->schema([
                    TextInput::make('publisher')->label(__('item.field_publisher')),
                    TextInput::make('series_title')->label(__('item.field_series_title')),
                    TextInput::make('issue_number')->label(__('item.field_issue_number')),
                    DatePicker::make('cover_date')->label(__('item.field_cover_date')),
                ])
                ->columns(2),
        ];
    }
}
