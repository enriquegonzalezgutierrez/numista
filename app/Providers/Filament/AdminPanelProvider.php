<?php

// app/Providers/Filament/AdminPanelProvider.php

namespace App\Providers\Filament;

use App\Http\Middleware\CheckSubscriptionStatus;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Filament\Pages\Dashboard;
use Numista\Collection\UI\Filament\Pages\Tenancy\EditTenantProfile;
use Numista\Collection\UI\Filament\Pages\Tenancy\RegisterTenant;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Teal,
                'gray' => Color::Slate,
            ])
            ->font('Poppins')
            ->brandName('Numista App')
            ->brandLogo(asset('storage/logo.png'))
            ->brandLogoHeight('48px')
            ->favicon(asset('storage/favicon.png'))
            ->discoverResources(in: base_path('src/Collection/UI/Filament/Resources'), for: 'Numista\\Collection\\UI\\Filament\\Resources')
            ->discoverPages(in: base_path('src/Collection/UI/Filament/Pages'), for: 'Numista\\Collection\\UI\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: base_path('src/Collection/UI/Filament/Widgets'), for: 'Numista\\Collection\\UI\\Filament\\Widgets')
            ->widgets([
                //
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
            ->tenantMiddleware([
                CheckSubscriptionStatus::class,
            ], isPersistent: true)
            ->tenantRegistration(RegisterTenant::class)
            ->tenantProfile(EditTenantProfile::class)

            // Add the user menu items using translation keys.
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(__('panel.user_menu_profile'))
                    ->url(fn (): string => EditTenantProfile::getUrl())
                    ->icon('heroicon-o-user-circle'),
                'subscription' => MenuItem::make()
                    ->label(__('panel.user_menu_subscription'))
                    ->url(fn (): string => route('my-account.subscription.manage'))
                    ->icon('heroicon-o-credit-card'),
                // Use the key from the lang/es.json file for logout.
                'logout' => MenuItem::make()->label(__('Log Out')),
            ]);
    }
}
