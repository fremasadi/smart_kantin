<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\OrderItem;
use App\Observers\OrderItemObserver;
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
        OrderItem::observe(OrderItemObserver::class);

    }
}
