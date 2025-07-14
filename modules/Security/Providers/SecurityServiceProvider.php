<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\Providers;

use Illuminate\Support\ServiceProvider;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Security module services here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        // Register GraphQL schema
        config(['lighthouse.schema.register' => array_merge(
            config('lighthouse.schema.register', []),
            [__DIR__ . '/../GraphQL/schema.graphql']
        )]);
    }
}
