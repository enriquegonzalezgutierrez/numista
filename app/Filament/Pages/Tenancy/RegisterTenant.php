<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Tenant;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Illuminate\Support\Str;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register new Collection'; // Custom label
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
                    ->unique(Tenant::class, 'slug'),
            ]);
    }

    protected function handleRegistration(array $data): Tenant
    {
        $tenant = Tenant::create($data);

        $tenant->users()->attach(auth()->user());

        return $tenant;
    }
}