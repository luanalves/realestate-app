<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registers application services.
     *
     * Intended for binding services or performing setup tasks during application registration.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstraps application services after all service providers have been registered.
     */
    public function boot(): void
    {
    }
}
