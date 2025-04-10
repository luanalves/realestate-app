<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Remove the ignoreRoutes() call as we want Passport routes to be registered
        // Passport::ignoreRoutes();
        Passport::enablePasswordGrant();

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Caminho das migrations do módulo
        $this->loadMigrationsFrom(base_path('modules/UserManagement/Database/Migrations'));
    }
}
