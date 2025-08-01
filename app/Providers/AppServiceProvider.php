<?php

// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Policies\AddressPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Numista\Collection\Application\Listeners\SendNewOrderNotificationToSeller;
use Numista\Collection\Application\Listeners\SendOrderConfirmationEmail;
use Numista\Collection\Application\Listeners\SendSubscriptionConfirmationEmail;
use Numista\Collection\Application\Listeners\UpdateSoldItemStatus;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Events\SubscriptionActivated;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection as ModelsCollection; // Alias to avoid conflict
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\Domain\Observers\CategoryObserver;
use Numista\Collection\Domain\Observers\CollectionObserver;
use Numista\Collection\Domain\Observers\ItemObserver;
use Numista\Collection\Domain\Observers\TenantObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Item::observe(ItemObserver::class);
        Category::observe(CategoryObserver::class);
        Tenant::observe(TenantObserver::class);
        ModelsCollection::observe(CollectionObserver::class);

        Gate::policy(Address::class, AddressPolicy::class);

        Carbon::setLocale(config('app.locale'));

        Blade::if('active', function (string $routePattern) {
            return request()->routeIs($routePattern);
        });

        // Manually register custom domain event listeners
        Event::listen(OrderPlaced::class, SendOrderConfirmationEmail::class);
        Event::listen(OrderPlaced::class, UpdateSoldItemStatus::class);
        Event::listen(OrderPlaced::class, SendNewOrderNotificationToSeller::class);
        Event::listen(SubscriptionActivated::class, SendSubscriptionConfirmationEmail::class);

        // NOTE: The listener for the native `Registered` event has been moved
        // to the EventServiceProvider for better organization and reliability.
    }
}
