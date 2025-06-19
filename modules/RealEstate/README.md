# RealEstate Module

This module handles real estate agencies management in the application. It provides functionality to create, update, delete, and manage real estate agencies.

## Features

- Create real estate agencies
- Update real estate agency information
- Delete real estate agencies
- List real estate agencies
- Search real estate agencies by ID
- Multi-tenant support (agencies are isolated by tenant)

## Structure

- **Database/**: Migrations and seeders
- **GraphQL/**: GraphQL schema, mutations, and queries
- **Models/**: Eloquent models
- **Providers/**: Service providers
- **Services/**: Business logic services
- **Tests/**: Unit and feature tests

## GraphQL Operations

### Queries
- `realEstates`: List all real estate agencies
- `realEstateById(id: ID!)`: Get a specific real estate agency by ID

### Mutations
- `createRealEstate(input: CreateRealEstateInput!)`: Create a new real estate agency
- `updateRealEstate(id: ID!, input: UpdateRealEstateInput!)`: Update an existing real estate agency
- `deleteRealEstate(id: ID!)`: Delete a real estate agency

## Security

All operations are protected with appropriate authentication and authorization:
- Users can only manage real estate agencies within their tenant
- Only users with appropriate roles can perform CRUD operations
- All GraphQL operations use the `@auth` directive

## Multi-Tenant Support

The module supports multi-tenant architecture where:
- Each real estate agency belongs to a tenant
- Users can only access agencies within their tenant
- Super admins can access all agencies across tenants

## Testing

The module includes comprehensive tests:
- Unit tests for models and services
- Feature tests for GraphQL operations
- Authentication and authorization tests
- Multi-tenant isolation tests

Always run tests after making changes:
```bash
cd ../realestate-infra && docker compose exec app php artisan test --filter=RealEstate
```
