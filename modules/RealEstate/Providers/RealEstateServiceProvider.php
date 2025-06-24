<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Organization\Contracts\OrganizationTypeRegistryContract;
use Modules\Organization\Providers\OrganizationServiceProvider;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Support\RealEstateConstants;

class RealEstateServiceProvider extends ServiceProvider
{
    /**
     * Todas as dependências de serviço do pacote.
     *
     * @var array
     */
    protected $dependencies = [
        OrganizationServiceProvider::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Registre todas as dependências primeiro
        foreach ($this->dependencies as $dependency) {
            $this->app->register($dependency);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar o tipo RealEstate no sistema de organizações
        $this->registerOrganizationType();
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Register GraphQL schema
        $schemaPath = __DIR__.'/../GraphQL/schema.graphql';
        if (file_exists($schemaPath)) {
            $currentSchemas = config('lighthouse.schema.register', []);

            config(['lighthouse.schema.register' => array_merge(
                $currentSchemas,
                [$schemaPath]
            )]);

            // Log registration for debugging
            \Log::debug('RealEstate schema registered', [
                'schema_path' => $schemaPath,
                'registered_schemas' => config('lighthouse.schema.register'),
            ]);
        }
    }

    /**
     * Registra o tipo RealEstate no sistema de organizações
     */
    protected function registerOrganizationType(): void
    {
        $registry = $this->app->make(OrganizationTypeRegistryContract::class);
        $registry->registerType(RealEstateConstants::ORGANIZATION_TYPE, RealEstate::class);
    }
}
