<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Providers;

use Illuminate\Support\ServiceProvider;

class UserManagementServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register UserRepositoryInterface with automatic factory resolution
        $this->app->bind(
            \Modules\UserManagement\Contracts\UserRepositoryInterface::class,
            function ($app) {
                return \Modules\UserManagement\Factories\UserRepositoryFactory::create();
            }
        );

        // Register UserService as singleton for better performance
        $this->app->singleton(
            \Modules\UserManagement\Services\UserService::class,
            function ($app) {
                return new \Modules\UserManagement\Services\UserService(
                    $app->make(\Modules\UserManagement\Contracts\UserRepositoryInterface::class)
                );
            }
        );

        // Keep existing binding for backward compatibility
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
        $this->bootCommands();
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        // Register GraphQL schema
        config(['lighthouse.schema.register' => array_merge(
            config('lighthouse.schema.register', []),
            [__DIR__ . '/../GraphQL/schema.graphql']
        )]);
    }

    /**
     * Register the module's routes.
     */
    protected function bootRoutes(): void
    {
        // UserManagement uses GraphQL exclusively
        // No web routes needed at this time
        // Future web routes can be added here when needed
    }

    /**
     * Register the module's commands.
     */
    protected function bootCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\UserManagement\Console\Commands\UserCacheCommand::class,
                \Modules\UserManagement\Console\Commands\TokenAnalysisCommand::class,
            ]);
        }
    }
}
