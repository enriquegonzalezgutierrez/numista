<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade; // Import the Blade facade
use Illuminate\Support\ServiceProvider;
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

        // Set Carbon's locale globally
        Carbon::setLocale(config('app.locale'));

        // THE FIX: Register a custom Blade directive for checking active routes
        Blade::if('active', function (string $routePattern) {
            return request()->routeIs($routePattern);
        });
    }
}
