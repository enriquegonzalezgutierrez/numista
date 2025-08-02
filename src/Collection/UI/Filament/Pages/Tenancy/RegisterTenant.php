<?php

// src/Collection/UI/Filament/Pages/Tenancy/RegisterTenant.php

namespace Numista\Collection\UI\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Tenant;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return __('panel.page_register_tenant_title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('panel.field_collection_name'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label(__('panel.field_collection_slug'))
                    ->required()
                    ->unique(Tenant::class, 'slug', ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    /**
     * THE FIX: Override the default redirect URL after tenant registration.
     * Instead of going to the dashboard, we send the user to the subscription page.
     */
    protected function getRedirectUrl(): string
    {
        return route('subscription.create', ['tenant' => $this->tenant]);
    }
}
