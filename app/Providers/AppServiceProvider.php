<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Observers\CategoryObserver;
use Numista\Collection\Domain\Observers\ItemObserver;

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
    }
}
