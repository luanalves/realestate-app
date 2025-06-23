<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Providers;

use Illuminate\Support\ServiceProvider;

class OrganizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar o caminho para os testes do módulo
        if ($this->app->runningInConsole()) {
            $this->registerTests();
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
    
    /**
     * Registra os testes do módulo para serem executados pelo PHPUnit
     */
    protected function registerTests(): void
    {
        // Adiciona o diretório de testes do módulo ao PHPUnit
        $this->publishes([
            __DIR__ . '/../Tests' => base_path('tests'),
        ], 'organization-tests');
    }
}
