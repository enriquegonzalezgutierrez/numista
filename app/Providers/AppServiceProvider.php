<?php

namespace App\Providers;

use App\Policies\AddressPolicy; // THE FIX: Import the policy
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate; // THE FIX: Import the Gate facade
use Illuminate\Support\ServiceProvider;
use Numista\Collection\Application\Listeners\SendOrderConfirmationEmail;
use Numista\Collection\Application\Listeners\UpdateSoldItemStatus;
use Numista\Collection\Domain\Events\OrderPlaced;
use Numista\Collection\Domain\Models\Address; // THE FIX: Import the model
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Collection;
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
        Collection::observe(CollectionObserver::class);

        // THE FIX: Register policies here
        Gate::policy(Address::class, AddressPolicy::class);

        // Set Carbon's locale globally
        Carbon::setLocale(config('app.locale'));

        Blade::if('active', function (string $routePattern) {
            return request()->routeIs($routePattern);
        });

        // Manually register event listeners
        Event::listen(
            OrderPlaced::class,
            SendOrderConfirmationEmail::class
        );

        Event::listen(
            OrderPlaced::class,
            UpdateSoldItemStatus::class
        );
    }
}
