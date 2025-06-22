<?php

namespace Numista\Collection\UI\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Illuminate\Support\Str;
use Numista\Collection\Domain\Models\Tenant;

class EditTenantProfile extends BaseEditTenantProfile
{
    public static function getLabel(): string
    {
        return __('panel.page_edit_tenant_title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('panel.field_collection_name'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label(__('panel.field_collection_slug'))
                    ->required()
                    ->unique(Tenant::class, 'slug', ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}
