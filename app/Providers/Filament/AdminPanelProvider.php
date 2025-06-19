<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Pages\Tenancy\RegisterTenant;
use Numista\Collection\UI\Filament\Pages\Tenancy\EditTenantProfile;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            // --- START OF CUSTOMIZATION ---
            ->colors([
                'primary' => Color::Teal,
                'gray' => Color::Slate,
            ])
            ->font('Poppins')
            ->brandName('Numista App')
            ->brandLogo(asset('storage/logo.png'))
            ->favicon(asset('storage/favicon.png'))
            // --- END OF CUSTOMIZATION ---
            ->discoverResources(in: base_path('src/Collection/UI/Filament/Resources'), for: 'Numista\\Collection\\UI\\Filament\\Resources')
            ->discoverPages(in: base_path('src/Collection/UI/Filament/Pages'), for: 'Numista\\Collection\\UI\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: base_path('src/Collection/UI/Filament/Widgets'), for: 'Numista\\Collection\\UI\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->tenant(
                model: Tenant::class,
                slugAttribute: 'slug',
                ownershipRelationship: 'tenants'
            )
            ->tenantRegistration(RegisterTenant::class)
            ->tenantProfile(EditTenantProfile::class);
    }
}
