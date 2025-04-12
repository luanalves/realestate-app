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

## Dependencies

- Always specify explicit versions in `composer.json` and `package.json`
- Document any third-party integrations in module README files
