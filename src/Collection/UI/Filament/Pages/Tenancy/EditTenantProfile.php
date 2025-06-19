<?php

namespace Numista\Collection\UI\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Illuminate\Support\Str;

class EditTenantProfile extends BaseEditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Collection profile'; // Custom label
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Collection Name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->label('URL Slug')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }
}