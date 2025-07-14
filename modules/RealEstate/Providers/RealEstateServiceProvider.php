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
        // Register the update service
        $this->app->bind(
            \Modules\RealEstate\Services\RealEstateUpdateService::class,
            \Modules\RealEstate\Services\RealEstateUpdateService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register event listeners
        $this->registerEventListeners();

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Register GraphQL schema
        $this->registerGraphQLSchema();
    }

    /**
     * Register GraphQL schema.
     */
    protected function registerGraphQLSchema(): void
    {
        $schemaPath = __DIR__.'/../GraphQL/schema.graphql';
        if (file_exists($schemaPath)) {
            $currentSchemas = config('lighthouse.schema.register', []);

            config(['lighthouse.schema.register' => array_merge(
                $currentSchemas,
                [$schemaPath]
            )]);
        }
    }

    /**
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {
        // Listener para injetar dados de RealEstate nas consultas da Organization
        $this->app['events']->listen(
            \Modules\Organization\Events\OrganizationDataRequested::class,
            \Modules\RealEstate\Listeners\InjectRealEstateDataListener::class
        );

        // Listener para criar registros RealEstate quando Organization é criada com extensionData
        $this->app['events']->listen(
            \Modules\Organization\Events\OrganizationCreated::class,
            \Modules\RealEstate\Listeners\CreateRealEstateOnOrganizationCreatedListener::class
        );

        // Listener para atualizar registros RealEstate quando Organization é atualizada com extensionData
        $this->app['events']->listen(
            \Modules\Organization\Events\OrganizationUpdated::class,
            \Modules\RealEstate\Listeners\UpdateRealEstateOnOrganizationUpdatedListener::class
        );
    }
}
