<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * As políticas de autorização da aplicação.
     */
    protected $policies = [
        'App\\Models\\Model' => 'App\\Policies\\ModelPolicy',
    ];

    /**
     * Registro de serviços de autenticação.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        // Passport::routes();
    }
}
