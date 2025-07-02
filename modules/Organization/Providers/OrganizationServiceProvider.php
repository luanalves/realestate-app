<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Organization\Contracts\OrganizationTypeRegistryContract;
use Modules\Organization\Services\OrganizationTypeRegistry;

class OrganizationServiceProvider extends ServiceProvider
{
    /**
     * Registers the Organization module's services in the application container.
     *
     * Binds the OrganizationTypeRegistryContract interface to its implementation as a singleton.
     * If the application is running in the console, registers the module's test directory for PHPUnit.
     */
    public function register(): void
    {
        // Registrar o serviço de registro de tipos de organização
        $this->app->singleton(OrganizationTypeRegistryContract::class, OrganizationTypeRegistry::class);
        
        // Registrar o caminho para os testes do módulo
        if ($this->app->runningInConsole()) {
            $this->registerTests();
        }
    }

    /**
     * Boots the Organization module by loading its database migrations and registering its GraphQL schema.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->registerGraphQLSchema();
    }
    
    /**
     * Registers the module's GraphQL schema with the Lighthouse configuration if the schema file exists.
     */
    protected function registerGraphQLSchema(): void
    {
        $schemaPath = __DIR__ . '/../GraphQL/schema.graphql';
        
        if (file_exists($schemaPath)) {
            $currentSchemas = config('lighthouse.schema.register', []);
            $currentSchemas[] = $schemaPath;
            config(['lighthouse.schema.register' => $currentSchemas]);
        }
    }
    
    /**
     * Publishes the module's test directory to the application's base tests directory for PHPUnit execution.
     *
     * This enables the module's tests to be run as part of the application's test suite under the 'organization-tests' tag.
     */
    protected function registerTests(): void
    {
        // Adiciona o diretório de testes do módulo ao PHPUnit
        $this->publishes([
            __DIR__ . '/../Tests' => base_path('tests'),
        ], 'organization-tests');
    }
}
