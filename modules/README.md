# Module Development Guidelines

This document provides guidelines and standards for developing modules in the Real Estate application.

## Module Structure

Each module should follow this standard structure:

```
ModuleName/
├── Console/
│   └── Commands/
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── GraphQL/
│   ├── Mutations/
│   ├── Queries/
│   └── schema.graphql
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Providers/
│   └── ModuleNameServiceProvider.php
├── Services/
├── Tests/
└── README.md
```

## GraphQL Implementation

Each module must follow the modular GraphQL pattern as defined in [ADR-008](../doc/architectural-decision-records/008-graphql-module-pattern.md). 

### Key Requirements:

1. **Module Schema File**
   - Create a `schema.graphql` file in the module's `GraphQL/` directory
   - Define all the module's types, inputs, queries, and mutations in this file
   - Use `extend type Query` and `extend type Mutation` rather than redefining these types

2. **Schema Registration**
   - Register the module's schema in the module's ServiceProvider:
   ```php
   // In ModuleNameServiceProvider.php boot() method
   config(['lighthouse.schema.register' => array_merge(
       config('lighthouse.schema.register', []),
       [__DIR__ . '/../GraphQL/schema.graphql']
   )]);
   ```

3. **Schema Import**
   - The module's schema is automatically imported via the registration mechanism
   - For clarity, it's also explicitly imported in the main schema file:
   ```graphql
   # In /graphql/schema.graphql
   #import ../modules/ModuleName/GraphQL/schema.graphql
   ```

4. **Resolvers**
   - Create resolver classes in `GraphQL/Queries/` and `GraphQL/Mutations/` directories
   - Use namespaced paths in the `@field` directive:
   ```graphql
   @field(resolver: "Modules\\ModuleName\\GraphQL\\Queries\\QueryName")
   ```

## ServiceProvider Requirements

Every module must have a ServiceProvider that:

1. Registers module-specific services and bindings
2. Loads module migrations from `Database/Migrations/`
3. **Registers the GraphQL schema** as shown above
4. (Optional) Registers module-specific commands
5. (Optional) Loads module-specific routes

## Example ServiceProvider

```php
<?php

declare(strict_types=1);

namespace Modules\ModuleName\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleNameServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register module-specific bindings
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        // Register GraphQL schema
        config(['lighthouse.schema.register' => array_merge(
            config('lighthouse.schema.register', []),
            [__DIR__ . '/../GraphQL/schema.graphql']
        )]);
        
        // Other bootstrapping...
    }
}
```

## Testing Module GraphQL Endpoints

For testing GraphQL endpoints in your module, follow these steps:

1. Create test classes in your module's `Tests/` directory
2. Follow the standard test structure as defined in the project's testing guidelines
3. Test both successful operations and error cases
4. Mock authentication and dependencies as needed
5. Run tests within the Docker container:

```bash
cd ../realestate-infra && docker compose exec app php artisan test --filter=ModuleNameTest
```

See the [project's testing guidelines](../tests/README.md) for more details.
