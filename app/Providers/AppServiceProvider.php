<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
        // Safety: Share null defaults to prevent undefined variable errors
        // until WS-2 is fully implemented
        View::share([
            'defaultHeader' => null,
            'defaultFooter' => null,
            'customHeader' => null,
            'customFooter' => null,
            'customHeaderPosition' => 'below',
        ]);

        // Fix for MariaDB / older MySQL with default string length
        Schema::defaultStringLength(191);
    }
}