<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Providers;

use Illuminate\Support\ServiceProvider;

class RealEstateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Register GraphQL schema
        $schemaPath = __DIR__.'/../GraphQL/schema.graphql';
        $currentSchemas = config('lighthouse.schema.register', []);

        // Debug information
        \Log::debug('Registering RealEstate schema', [
            'schema_path' => $schemaPath,
            'current_schemas' => $currentSchemas,
        ]);

        config(['lighthouse.schema.register' => array_merge(
            $currentSchemas,
            [$schemaPath]
        )]);

        // Verify registration
        \Log::debug('After RealEstate schema registration', [
            'registered_schemas' => config('lighthouse.schema.register'),
        ]);
    }
}
