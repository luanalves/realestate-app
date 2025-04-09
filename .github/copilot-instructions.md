# GitHub Copilot Instructions for Real Estate App

## Project Architecture

- **Framework**: Laravel
- **Architecture**: Modular architecture
- **Languages**: PHP, JavaScript, SQL
- **Package Managers**: Composer, NPM, Pip (Python)
- **Infrastructure**: Docker containerized application

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

## Naming Conventions

- **Classes**: PascalCase (e.g., `UserController`)
- **Methods**: camelCase (e.g., `getUserById()`)
- **Variables**: camelCase (e.g., `$userName`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `ROLE_SUPER_ADMIN`)
- **Database tables**: snake_case, plural (e.g., `user_profiles`)

## Code Practices

- Use constants for values that are reused across the application
- Document complex logic with comments
- Write tests for critical functionality
- Keep controllers thin, use services for business logic

## User Management Module

This module handles user authentication, roles, and permissions:

- Constants for roles are defined in `RolesSeeder.php`
- User creation and management handled in appropriate controllers
- Role hierarchy: super_admin > real_estate_admin > real_estate_agent > client

## Database Seeding

- The main `DatabaseSeeder` automatically discovers and calls all module seeders
- Role constants should be referenced from `RolesSeeder` class (e.g., `RolesSeeder::ROLE_SUPER_ADMIN`)
- Avoid hardcoding values that are defined as constants

## Dependencies

- Always specify explicit versions in `composer.json` and `package.json`
- Document any third-party integrations in module README files
