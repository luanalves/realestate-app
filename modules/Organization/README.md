# Organization Module

The Organization module provides a generic foundation for managing organizations in the application. It serves as a base module for any type of organization (real estate agencies, companies, etc.) following a modular and extensible architecture.

## Architecture Overview

This module implements a **generic organization system** where:
- **Organization** is the base model containing common fields for any organization type
- Specific organization types (like RealEstate) extend functionality through relationships
- Dynamic type registration system allows any module to register its organization type
- Complete independence from specific organization implementations

## Key Features

- Generic organization data management (name, CNPJ, contact info, etc.)
- Dynamic organization type registration system
- Organization membership management
- Trait-based functionality for organization relationships
- Independent architecture - no dependencies on specific organization types

## Module Structure

```
Organization/
├── Contracts/
│   └── OrganizationTypeRegistryContract.php    # Interface for type registration
├── Database/
│   ├── Migrations/
│   │   └── 2025_06_23_222813_create_organizations_table.php
│   └── Seeders/
├── GraphQL/
│   └── schema.graphql                           # GraphQL schema definitions
├── Models/
│   ├── Organization.php                         # Base organization model
│   └── OrganizationMembership.php              # User-organization relationships
├── Providers/
│   └── OrganizationServiceProvider.php         # Service registration
├── Services/
│   └── OrganizationTypeRegistry.php            # Dynamic type registration
├── Support/
│   └── OrganizationConstants.php               # Generic constants
├── Tests/
│   └── Unit/Models/
└── Traits/
    ├── BelongsToOrganizations.php              # User trait for organization membership
    └── HasOrganizationMemberships.php          # Organization trait for members
```

## Core Concepts

### 1. Generic Organization Model

The `Organization` model contains fields common to all organization types:

```php
class Organization extends Model
{
    protected $fillable = [
        'name',                 // Organization name
        'fantasy_name',         // Trade name/DBA
        'cnpj',                // Brazilian tax ID
        'description',          // Organization description
        'email',               // Contact email
        'phone',               // Contact phone
        'website',             // Website URL
        'active',              // Active status
        'organization_type',   // Type identifier (RealEstate, etc.)
    ];
}
```

### 2. Dynamic Type Registration

Other modules can register their organization types using the `OrganizationTypeRegistry`:

```php
// In a module's ServiceProvider
public function boot(): void
{
    $registry = $this->app->make(OrganizationTypeRegistryContract::class);
    $registry->registerType('RealEstate', RealEstate::class);
}
```

### 3. Organization Membership

Users can be members of organizations with specific roles:

```php
class OrganizationMembership extends Model
{
    protected $fillable = [
        'user_id',
        'organization_type',
        'organization_id',
        'role',
        'position',
        'is_active',
        'joined_at',
    ];
}
```

## Available Traits

### HasOrganizationMemberships

For organization models to manage their members:

```php
class Organization extends Model
{
    use HasOrganizationMemberships;
    
    // Now has access to:
    // - memberships()
    // - members()
    // - activeMemberships()
    // - hasMember($user)
}
```

### BelongsToOrganizations

For User models to access their organizations:

```php
class User extends Model
{
    use BelongsToOrganizations;
    
    // Now has access to:
    // - organizationMemberships()
    // - organizations()
    // - activeOrganizations()
    // - organizationsOfType($type)
}
```

## GraphQL Integration

The module provides a comprehensive GraphQL interface for organizations with the following main types:

```graphql
type Organization {
    id: ID!
    name: String!
    type: String!
    description: String
    email: String
    phone: String
    website: String
    isActive: Boolean!
    foundedAt: Date
    createdAt: DateTime!
    updatedAt: DateTime!
    members: [OrganizationMembership!]!
    addresses: [OrganizationAddress!]!
}

type OrganizationMembership {
    id: ID!
    organizationId: ID!
    userId: ID!
    role: String!
    joinedAt: DateTime!
    user: User!
    organization: Organization!
}

type OrganizationAddress {
    id: ID!
    organizationId: ID!
    street: String!
    number: String!
    complement: String
    district: String!
    city: String!
    state: String!
    zipCode: String!
    country: String!
    isMainAddress: Boolean!
    addressType: String!
    createdAt: DateTime!
    updatedAt: DateTime!
}
```

### Available Operations

- **Queries**: `organization`, `organizations`, `organizationAddressById`, `addressesByOrganizationId`
- **Member Operations**: `addOrganizationMember`, `updateOrganizationMember`, `removeOrganizationMember`
- **Address Operations**: `createOrganizationAddress`, `updateOrganizationAddress`, `deleteOrganizationAddress`

Note: The base Organization module does not provide the mutations for organization creation/update/deletion. These should be implemented in concrete implementations.

All operations require authentication using Laravel Passport tokens.

## Usage Examples

### Registering a New Organization Type

```php
// In YourModuleServiceProvider.php
public function boot(): void
{
    $registry = $this->app->make(OrganizationTypeRegistryContract::class);
    $registry->registerType('YourType', YourOrganizationModel::class);
}
```

### Creating Organization Memberships

```php
OrganizationMembership::create([
    'user_id' => $user->id,
    'organization_type' => 'RealEstate',
    'organization_id' => $organizationId,
    'role' => OrganizationConstants::ROLE_ADMIN,
    'position' => 'Manager',
    'is_active' => true,
    'joined_at' => now(),
]);
```

## Constants

Available role constants:

```php
OrganizationConstants::ROLE_ADMIN     // Administrator
OrganizationConstants::ROLE_MANAGER   // Manager  
OrganizationConstants::ROLE_MEMBER    // Member
```

Available address type constants:

```php
OrganizationConstants::ADDRESS_TYPE_HEADQUARTERS  // Main office
OrganizationConstants::ADDRESS_TYPE_BRANCH       // Branch office
```

## GraphQL API Documentation

For complete GraphQL API documentation including all queries, mutations, and examples, see:

📖 **[GraphQL API Documentation](doc/GraphQL_API.md)**

This documentation includes:
- All available queries and mutations with examples
- Request/response formats and variable structures
- cURL commands for testing all endpoints
- Error handling examples and common responses
- Complete workflow examples for organization management
- Authentication requirements and token usage

## Testing

Run module-specific tests:

```bash
# Run all Organization module tests
docker compose exec app php artisan test --filter=Organization

# Run specific test class
docker compose exec app php artisan test --filter=OrganizationMembershipTest
```

## Dependencies

- Laravel Framework
- Laravel Passport (for authentication)
- Lighthouse GraphQL

## Extension Points

To create a new organization type:

1. Create your specific model that relates to Organization
2. Register your type in your module's ServiceProvider
3. Implement specific GraphQL types that implement the Organization interface
4. Add your specific business logic in services

## Migration Notes

This module follows the **composition pattern** rather than inheritance:
- Specific organization types (like RealEstate) have a `belongsTo` relationship with Organization
- This allows for flexible data modeling and better database performance
- Each organization type can have its own specific fields while sharing common ones

## Coding Standards

This module follows:
- **English-only code**: All variables, methods, classes, and comments in English
- **PSR-12** coding standards
- **SOLID principles**
- **Clean code** practices with minimal comments
- **Self-documenting code** through descriptive naming
