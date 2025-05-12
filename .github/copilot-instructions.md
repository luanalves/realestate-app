---
applyTo: "**"
---
# GitHub Copilot Instructions for Real Estate App

## Project Architecture

- **Framework**: Laravel with GraphQL using Lighthouse PHP
- **Architecture**: Modular architecture
- **Languages**: PHP, JavaScript, SQL
- **Package Managers**: Composer, NPM, Pip (Python)
- **Infrastructure**: Docker containerized application

## API Architecture

- **IMPORTANT**: This project uses GraphQL exclusively, NOT REST APIs
- All API endpoints must be implemented as GraphQL queries and mutations
- Do not create REST controllers or API routes
- Use Lighthouse PHP directives for simple CRUD operations
- Create custom resolver classes for complex operations

## GraphQL Implementation

- Schema files (.graphql) define the API contract and are located in each module's GraphQL directory
- Use schema-first development (define types before implementing resolvers)
- Create resolver classes in GraphQL/Queries and GraphQL/Mutations directories
- Extend existing types with `extend type Query` and `extend type Mutation`
- Implement authentication with `@auth` directive
- Test queries using GraphQL Playground at `/graphql-playground`

## Docker Environment

- The project uses Docker for local development and deployment
- Docker configuration files are located in `../realestate-infra/`
- To execute commands in the application container, use:
  ```bash
  cd ../realestate-infra && docker compose exec app [command]
  ```
- Example for running artisan commands:
  ```bash
  cd ../realestate-infra && docker compose exec app php artisan [command]
  ```
- All PHP commands should be executed in the Docker container, not locally

## Key Architectural Principles

1. **Modular Structure**
  - All modules are located in the `modules/` directory
  - Each module follows Laravel's standard directory structure
  - Modules should be independent and self-contained

2. **Database**
  - Each module has its own migrations and seeders
  - Constants for common data (like roles) are defined in their respective seeders
  - Use database seeders to populate initial data

3. **Code Organization**
  - Follow PSR-12 coding standards
  - Use dependency injection where appropriate
  - Utilize Laravel service providers for module registration

## Naming Conventions
- Use PascalCase for component names, interfaces, and type aliases
- Use camelCase for variables, functions, and methods
- Prefix private class members with underscore (_)
- Use ALL_CAPS for constants

## Error Handling
- Use try/catch blocks for async operations
- Implement proper error boundaries in React components
- Always log errors with contextual information

## Module Structure

Each module should follow this structure:
```
ModuleName/
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── GraphQL/
│   ├── Mutations/
│   ├── Queries/
│   └── schema.graphql
├── Http/
│   ├── Controllers/ (Only for web controllers, NOT for API)
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Providers/
│   └── ModuleNameServiceProvider.php
├── Services/
└── Tests/
```

## Naming Conventions

- **Classes**: PascalCase (e.g., `UserResolver`)
- **GraphQL Types**: PascalCase (e.g., `User`, `CreateUserInput`)
- **GraphQL Fields**: camelCase (e.g., `userName`, `emailAddress`)
- **Methods**: camelCase (e.g., `getUserById()`)
- **Variables**: camelCase (e.g., `$userName`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `ROLE_SUPER_ADMIN`)
- **Database tables**: snake_case, plural (e.g., `user_profiles`)

## Code Practices

- Follow PHP Standards Recommendations (PSRs):
  - PSR-1: Basic Coding Standard
  - PSR-4: Autoloader
  - PSR-7: HTTP Message Interface
  - PSR-11: Container Interface
  - PSR-12: Extended Coding Style Guide
  - PSR-14: Event Dispatcher
- Use constants for values that are reused across the application
- Document complex logic with comments
- Write tests for critical functionality
- Keep resolvers thin, use services for business logic
- All PHP files must include `declare(strict_types=1);` at the top of the file
- All files must include the following comment block at the beginning:
  ```php
  /**
   * @author      Luan Silva
   * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
   * @license     https://www.thedevkitchen.com.br  Copyright
   */
  ```

## User Management Module

This module handles user authentication, roles, and permissions:

- Constants for roles are defined in `RolesSeeder.php`
- User creation and management handled through GraphQL mutations
- Role hierarchy: super_admin > real_estate_admin > real_estate_agent > client

## Authentication

- This project uses **Laravel Passport** for OAuth token authentication
- Authentication is integrated with GraphQL using the `@auth` directive from Lighthouse
- Token-based authentication is implemented for all API endpoints
- Access tokens are required in the Authorization header (Bearer token)
- User permissions are checked based on their assigned role
- Authentication directives should be used on all protected queries and mutations

### OAuth Token Endpoint

- OAuth token endpoint is available at `/oauth/token`
- Use the password grant type for user authentication
- Required parameters:
  - `grant_type=password`
  - `client_id` - From the `oauth_clients` table
  - `client_secret` - From the `oauth_clients` table
  - `username` - User's email address
  - `password` - User's password
- Common errors:
  - "invalid_grant" - Check if user exists and credentials are correct
  - "invalid_client" - Verify client_id and client_secret are valid
  - "invalid_request" - Ensure all required parameters are included

### Troubleshooting Authentication

- Ensure the Passport client exists in the database (`php artisan passport:client`)
- Verify user email and password are correct
- Check if user account is active and not locked
- For local development, use the test seeded users or create new ones via GraphQL

## GraphQL Development Process

1. Define new types and operations in the module's schema.graphql file
2. Create necessary input types for mutations
3. Implement resolver classes in GraphQL/Mutations or GraphQL/Queries directories
4. Use directives like @auth, @find, @all for common operations
5. Implement custom logic in resolver classes when needed

## Database Seeding

- The main `DatabaseSeeder` automatically discovers and calls all module seeders
- Role constants should be referenced from `RolesSeeder` class (e.g., `RolesSeeder::ROLE_SUPER_ADMIN`)
- Avoid hardcoding values that are defined as constants

## Testing Guidelines

### Test Structure

- Tests should be organized in the `tests/` directory following the Laravel convention:
  - `tests/Unit/` for unit tests that test isolated components
  - `tests/Feature/` for feature tests that test the application as a whole
  - Module-specific tests should be placed under a subdirectory matching the module name:
    ```
    tests/Feature/UserManagement/
    tests/Feature/Properties/
    tests/Feature/UserPreferences/
    ```

### GraphQL Test Requirements

When writing tests for GraphQL functionality:

1. **Test Isolation**
   - Tests should be independent of the actual database state
   - Use mocks instead of database interactions to avoid test failures due to data inconsistencies
   - Tests should pass regardless of the environment they run in

2. **Authentication in Tests**
   - Use Laravel Passport's `Passport::actingAs()` method to mock authentication
   - When using mock User objects with Passport, implement the following method expectations:
     ```php
     $mockUser = Mockery::mock(User::class)->makePartial();
     $mockUser->shouldReceive('getAuthIdentifier')->andReturn(1);
     $mockUser->shouldReceive('withAccessToken')->andReturnSelf();
     ```

3. **Test Organization**
   - Create separate test files based on functionality (e.g., UserGraphQLTest, UserGraphQLValidationTest)
   - For each API operation, test both successful scenarios and error cases
   - Use descriptive test method names that clearly indicate what's being tested

4. **Running Tests**
   - Tests must be executed within the Docker container:
     ```bash
     cd ../realestate-infra && docker compose exec app php artisan test
     ```
   - To run a specific test file:
     ```bash
     cd ../realestate-infra && docker compose exec app php artisan test --filter=UserGraphQLTest
     ```
   - To run a specific test method:
     ```bash
     cd ../realestate-infra && docker compose exec app php artisan test --filter=UserGraphQLTest::testQueryUsers
     ```

### Standard Test Structure

All test classes must follow this structure:

```php
<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Feature\ModuleName;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;

class FeatureGraphQLTest extends TestCase
{
    use WithFaker;
    
    /**
     * Mock user for testing
     */
    protected $mockUser;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock user for authentication
        $this->mockUser = Mockery::mock(User::class)->makePartial();
        $this->mockUser->shouldReceive('getAuthIdentifier')->andReturn(1);
        $this->mockUser->shouldReceive('withAccessToken')->andReturnSelf();
        
        // Authenticate with Laravel Passport
        Passport::actingAs($this->mockUser);
    }
    
    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test description here.
     */
    public function testFeatureName(): void
    {
        // Arrange - Set up test data
        // ...
        
        // Act - Perform the action being tested
        // ...
        
        // Assert - Verify the results
        $this->assertTrue(true, 'Descriptive assertion message');
    }
}
```

### Testing GraphQL Queries and Mutations

For testing GraphQL queries:

```php
/**
 * Test getting a list of resources through GraphQL.
 */
public function testQueryResources(): void
{
    // Prepare mock response data
    $expectedData = [
        'data' => [
            'resources' => [
                [
                    'id' => '1',
                    'name' => 'Resource Name',
                    'type' => 'Resource Type'
                ]
            ]
        ]
    ];

    // If making an actual request (use cautiously):
    $response = $this->postJson('/graphql', [
        'query' => '
            query {
                resources {
                    id
                    name
                    type
                }
            }
        '
    ]);
    
    // Assert response meets expectations
    $response->assertStatus(200)
        ->assertJson($expectedData);
}
```

### Testing Validation and Error Cases

For testing validation errors:

```php
/**
 * Test validation error when creating a resource with invalid data.
 */
public function testCreateResourceWithInvalidData(): void
{
    // Make the GraphQL request with invalid data
    $response = $this->postJson('/graphql', [
        'query' => '
            mutation {
                createResource(input: {
                    name: "",  # Invalid: empty name
                    type: "InvalidType"  # Invalid: wrong type
                }) {
                    id
                    name
                    type
                }
            }
        '
    ]);
    
    // Assert validation error is returned
    $response->assertJson([
        'errors' => [
            [
                'message' => 'Validation failed for the field [createResource].',
                'extensions' => [
                    'validation' => [
                        'input.name' => [
                            'The name field is required.'
                        ]
                    ]
                ]
            ]
        ]
    ]);
}
```

### Testing Authentication Requirements

For testing authentication requirements:

```php
/**
 * Test authentication is required for protected queries.
 */
public function testAuthenticationRequiredForQuery(): void
{
    // Remove authentication
    Passport::actingAs(null);
    
    // Make the GraphQL request without authentication
    $response = $this->postJson('/graphql', [
        'query' => '
            query {
                protectedResources {
                    id
                    name
                }
            }
        '
    ]);
    
    // Assert that authentication error is returned
    $response->assertJson([
        'errors' => [
            [
                'message' => 'Unauthenticated.'
            ]
        ]
    ]);
}
```

### Test Coverage Requirements

- All GraphQL queries must have corresponding tests
- All GraphQL mutations must have corresponding tests
- Test coverage should include:
  - Successful operations (happy path)
  - Authentication requirements
  - Validation errors
  - Authorization checks
  - Edge cases (e.g., non-existent resources)

### Testing Best Practices

1. Follow the Arrange-Act-Assert pattern in tests
2. Use meaningful assertions that verify actual behavior
3. Document complex test scenarios with comments
4. Each test method should focus on testing a single aspect of functionality
5. Prefer simpler, focused tests over complex, multi-assertion tests
6. Mock external dependencies when appropriate
7. Keep tests independent of each other
8. Test both success and failure scenarios

## Dependencies

- Always specify explicit versions in `composer.json` and `package.json`
- Document any third-party integrations in module README files

## Help Commands

### update password
```php
app php artisan tinker
$user = App\Models\User::where('email', 'contato@thedevkitchen.com.br')->first();
$user->password = Hash::make('senha123');
$user->save();
```

### Create oauth token
```php
php artisan passport:client --client
```