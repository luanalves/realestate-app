<?php

namespace Modules\UserManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UserManagementServiceProvider extends ServiceProvider
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
        $this->bootRoutes();
    }

    /**
     * Register the module's routes.
     */
    protected function bootRoutes(): void
    {
        if (! $this->app->routesAreCached()) {
            Route::prefix('user-management')
                ->middleware('web')
                ->group(base_path('routes/user_management.php'));
        }
    }
}
