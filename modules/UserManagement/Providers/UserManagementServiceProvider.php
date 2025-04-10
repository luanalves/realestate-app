<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserManagementServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register our user service in the service container
        $this->app->bind('modules\UserManagement\Services\UserService', function () {
            return new \modules\UserManagement\Services\UserService();
        });
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
            // Load web routes
            Route::prefix('user-management')
                ->middleware('web')
                ->group(base_path('routes/user_management.php'));
            
            // Load module API routes
            Route::middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }
    }
}
