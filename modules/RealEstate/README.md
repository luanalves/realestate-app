 # RealEstate Module

The RealEstate module handles real estate agency management in the application. It extends the generic Organization module to provide specialized functionality for real estate agencies (imobiliárias) while maintaining a clean, modular architecture.

## Architecture Overview

This module implements a **specialized organization type** that:
- **Extends Organization**: Uses the generic Organization module as foundation
- **Adds specific fields**: CRECI registration, state registration, etc.
- **Self-registers**: Automatically registers 'RealEstate' type in the organization system
- **Depends on Organization**: Clean dependency relationship (RealEstate → Organization)

## Key Features

- Real estate agency management
- CRECI (Brazilian real estate license) validation
- State registration tracking
- Integration with generic organization functionality
- **Organization Extension System**: Inject real estate data into organization queries
- GraphQL API for agency operations
- Multi-tenant support with proper isolation

## Module Structure

```
RealEstate/
├── Database/
│   ├── Migrations/
│   │   └── 2025_06_23_222826_create_real_estates_table.php
│   └── Seeders/
├── GraphQL/
│   ├── schema.graphql                           # RealEstate-specific GraphQL schema
│   ├── Mutations/
│   │   ├── CreateRealEstateResolver.php
│   │   ├── UpdateRealEstateResolver.php
│   │   └── DeleteRealEstateOrganizationResolver.php
│   └── Queries/
│       └── RealEstateResolver.php
├── Listeners/
│   └── InjectRealEstateDataListener.php        # Organization extension listener
├── Models/
│   └── RealEstate.php                           # Specialized real estate model
├── Providers/
│   └── RealEstateServiceProvider.php           # Auto-registration of type
├── Support/
│   └── RealEstateConstants.php                 # RealEstate-specific constants
├── Tests/
│   └── Unit/
│       └── RealEstateTest.php
└── README.md
```

## Data Model

The RealEstate model extends organization functionality through a relationship:

```php
class RealEstate extends Model
{
    // Specific real estate fields
    protected $fillable = [
        'creci',                // CRECI registration number
        'state_registration',   // State registration number
    ];
    
    // Relationship to base organization
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'id');
    }
    
    // Access organization fields via accessors
    public function getNameAttribute(): ?string
    {
        return $this->organization?->name;
    }
}
```

### Database Schema

**organizations table** (from Organization module):
- `id` - Primary key
- `name` - Agency name
- `fantasy_name` - Trade name
- `cnpj` - Brazilian tax ID
- `description` - Agency description
- `email` - Contact email
- `phone` - Contact phone
- `website` - Website URL
- `active` - Active status
- `organization_type` - Always 'RealEstate' for this module

**real_estates table** (RealEstate-specific):
- `id` - Primary key + Foreign key to organizations.id
- `creci` - CRECI registration number
- `state_registration` - State registration number

## GraphQL Operations

This module follows the modular GraphQL approach. Schema definitions are in `GraphQL/schema.graphql` and automatically imported into the main application schema.

### Queries

#### 1. `realEstates` - List Real Estate Agencies

Returns a paginated list of real estate agencies.

**Parameters:**
- `first: Int = 10` - Items per page (default: 10)
- `page: Int = 1` - Page number (default: 1)
- `orderBy: [OrderByClause!]` - Sorting options (optional)

**Returns:** `RealEstatePaginator!` - Paginator containing list of agencies

**Example:**
```graphql
query {
  realEstates(first: 5, page: 1, orderBy: [{column: "name", order: ASC}]) {
    data {
      id
      name
      fantasy_name
      cnpj
      email
      phone
      creci
      state_registration
      active
    }
    paginatorInfo {
      currentPage
      lastPage
      total
    }
  }
}
```

#### 2. `realEstateById` - Get Real Estate Agency by ID

Returns a specific real estate agency by its ID.

**Parameters:**
- `id: ID!` - Agency ID (required)

**Returns:** `RealEstate` - Agency details

**Example:**
```graphql
query {
  realEstateById(id: 1) {
    id
    name
    fantasy_name
    cnpj
    description
    email
    phone
    website
    creci
    state_registration
    active
    created_at
    updated_at
  }
}
```
### Mutations

#### 1. `createRealEstate` - Create New Real Estate Agency

Creates a new real estate agency in the system.

**Input Parameters (`CreateRealEstateInput`):**
- `name: String!` - Agency name (required)
- `fantasyName: String` - Trade name (optional)
- `cnpj: String!` - Brazilian tax ID (required, unique)
- `description: String` - Agency description (optional)
- `email: String!` - Contact email (required, unique)
- `phone: String` - Contact phone (optional)
- `website: String` - Agency website (optional)
- `creci: String` - CRECI registration number (optional)
- `stateRegistration: String` - State registration number (optional)
- `active: Boolean = true` - Active status (default: true)

**Returns:** `RealEstate!` - The newly created agency

**Example:**
```graphql
mutation {
  createRealEstate(input: {
    name: "Premium Real Estate"
    fantasyName: "Premium Realty"
    cnpj: "12345678901234"
    description: "Leading real estate agency in the city"
    email: "contact@premiumrealty.com"
    phone: "+55 11 99999-9999"
    website: "https://premiumrealty.com"
    creci: "CRECI-SP-123456"
    stateRegistration: "123.456.789.012"
    active: true
  }) {
    id
    name
    fantasy_name
    cnpj
    email
    creci
    state_registration
    active
  }
}
```

#### 2. `updateRealEstate` - Update Real Estate Agency

Updates an existing real estate agency.

**Input Parameters:**
- `id: ID!` - Agency ID (required)
- `input: UpdateRealEstateInput!` - Updated agency data

**Returns:** `RealEstate!` - The updated agency

**Example:**
```graphql
mutation {
  updateRealEstate(
    id: 1
    input: {
      name: "Updated Agency Name"
      phone: "+55 11 88888-8888"
      active: false
    }
  ) {
    id
    name
    phone
    active
    updated_at
  }
}
```

#### 3. `deleteRealEstate` - Delete Real Estate Agency

Deletes a real estate agency and its associated organization data.

**Parameters:**
- `id: ID!` - Agency ID (required)

**Returns:** `RealEstate!` - The deleted agency data

**Example:**
```graphql
mutation {
  deleteRealEstate(id: 1) {
    id
    name
    active
  }
}

## Organization Integration

The RealEstate module integrates with the Organization module's extension system, allowing real estate data to be accessed through organization queries.

### Extension System

The module implements a listener (`InjectRealEstateDataListener`) that automatically injects real estate-specific data when an organization is queried via GraphQL.

### Getting Organization + Real Estate Data

```graphql
query GetOrganizationWithRealEstate($id: ID!) {
  organization(id: $id) {
    id
    name
    description
    addresses {
      id
      street
      city
    }
    extensionData  # Contains real estate data
  }
}
```

### When to Use Each Approach

**Use Organization Extension:**
- Need complete organization + real estate data
- Want to access organization addresses
- Building user interfaces showing organizational information

**Use Direct RealEstate Queries:**
- Need only real estate specific data
- Building reports focused on real estate data
- Performing bulk operations on real estate entities

For detailed information, see [Organization Integration Documentation](doc/Organization_Integration.md).

## Constants

Available constants from `RealEstateConstants`:

```php
// Organization type
RealEstateConstants::ORGANIZATION_TYPE = 'RealEstate'

// CRECI status
RealEstateConstants::CRECI_STATUS_ACTIVE = 'active'
RealEstateConstants::CRECI_STATUS_INACTIVE = 'inactive'
RealEstateConstants::CRECI_STATUS_SUSPENDED = 'suspended'

// Valid Brazilian states
RealEstateConstants::VALID_STATES = ['AC', 'AL', 'AP', 'AM', ...]
```

## Service Registration

This module automatically registers itself with the Organization system:

```php
// In RealEstateServiceProvider::boot()
protected function registerOrganizationType(): void
{
    $registry = $this->app->make(OrganizationTypeRegistryContract::class);
    $registry->registerType(RealEstateConstants::ORGANIZATION_TYPE, RealEstate::class);
}
```

## Testing

Run module-specific tests:

```bash
# Run all RealEstate module tests
docker compose exec app php artisan test --filter=RealEstateTest

# Run specific test methods
docker compose exec app php artisan test --filter=testCreateRealEstate
docker compose exec app php artisan test --filter=testCascadeDelete
```

## Database Migrations

The module includes migrations for:

1. **2025_06_23_222826_create_real_estates_table.php**
   - Creates the `real_estates` table
   - Sets up foreign key relationship with `organizations` table
   - Implements cascade delete for data integrity

## Dependencies

This module depends on:

- **Organization Module** - Provides base organization functionality
- **Laravel Framework** - Core framework
- **Laravel Passport** - Authentication
- **Lighthouse GraphQL** - GraphQL implementation

## Integration Examples

### Creating a Real Estate Agency

```php
// Using the GraphQL resolver
$resolver = new CreateRealEstateResolver();
$realEstate = $resolver(null, [
    'input' => [
        'name' => 'Premium Real Estate',
        'cnpj' => '12345678901234',
        'email' => 'contact@premium.com',
        'creci' => 'CRECI-SP-123456',
        'stateRegistration' => '123.456.789.012'
    ]
]);
```

### Accessing Organization Data

```php
$realEstate = RealEstate::find(1);

// Access organization fields through accessors
echo $realEstate->name;          // From organization
echo $realEstate->fantasy_name;  // From organization
echo $realEstate->cnpj;         // From organization
echo $realEstate->creci;        // From real_estates table
```

### Working with Memberships

```php
// Add user to real estate agency
OrganizationMembership::create([
    'user_id' => $user->id,
    'organization_type' => RealEstateConstants::ORGANIZATION_TYPE,
    'organization_id' => $realEstate->id,
    'role' => OrganizationConstants::ROLE_ADMIN,
    'position' => 'General Manager',
    'is_active' => true,
    'joined_at' => now(),
]);
```

## Error Handling

The module handles common scenarios:

- **Validation errors** for required fields
- **Duplicate CNPJ/email** validation
- **Cascade deletion** when organization is deleted
- **Authentication requirements** for all operations

## Future Enhancements

Potential areas for expansion:

- Property listing management
- Agent management within agencies
- Commission tracking
- Integration with external real estate platforms
- Advanced reporting and analytics

## Coding Standards

This module follows:
- **English-only code**: All variables, methods, classes, and comments in English
- **PSR-12** coding standards
- **SOLID principles** 
- **Clean code** practices with minimal unnecessary comments
- **Self-documenting code** through descriptive naming
- **Modular architecture** with clear separation of concerns

## Related Documentation

- [Organization Module README](../Organization/README.md)
- [ADR-0006: Code Standards and PSR](../../doc/architectural-decision-records/0006-padroes-de-codigo-e-psr.md)
- [GraphQL Schema Documentation](./GraphQL/schema.graphql)

**Retorno:** `RealEstate!` - A imobiliária após a atualização

**Exemplo de chamada:**
```graphql
mutation {
  updateRealEstate(
    id: 1
    input: {
      name: "Imobiliária Exemplo Atualizada"
      email: "novo-contato@exemplo.com"
      phone: "1199997777"
      active: true
      address: {
        street: "Rua Nova, 500"
        city: "São Paulo"
        state: "SP"
      }
    }
  ) {
    id
    name
    email
    phone
    active
    address {
      street
      city
      state
    }
  }
}
```

#### 3. `deleteRealEstate` - Excluir Imobiliária

**Descrição:** Remove uma imobiliária do sistema.

**Parâmetros:**
- `id: ID!` - ID da imobiliária a ser excluída (obrigatório)

**Retorno:** `RealEstate!` - Os dados da imobiliária que foi excluída

**Exemplo de chamada:**
```graphql
mutation {
  deleteRealEstate(id: 1) {
    id
    name
    email
  }
}
```

## Tipos de Dados GraphQL

### Tipos Principais

1. **`RealEstate`** - Representa uma imobiliária
   - `id: ID!`
   - `name: String!`
   - `description: String`
   - `email: String!`
   - `phone: String`
   - `website: String`
   - `address: RealEstateAddress`
   - `creci: String`
   - `active: Boolean!`
   - `tenant_id: ID`
   - `created_at: DateTime!`
   - `updated_at: DateTime!`
   - `users: [User!]!` (relação com usuários)

2. **`RealEstateAddress`** - Representa o endereço de uma imobiliária
   - `street: String`
   - `city: String`
   - `state: String`
   - `zip_code: String`
   - `country: String`

3. **`RealEstatePaginator`** - Contém resultados paginados
   - `paginatorInfo: PaginatorInfo!`
   - `data: [RealEstate!]!`

### Tipos de Input

1. **`RealEstateAddressInput`** - Input para dados de endereço
   - `street: String`
   - `city: String`
   - `state: String`
   - `zip_code: String`
   - `country: String`

2. **`UpdateRealEstateInput`** - Input para atualização de imobiliária
   - `name: String`
   - `description: String`
   - `email: String`
   - `phone: String`
   - `website: String`
   - `address: RealEstateAddressInput`
   - `creci: String`
   - `active: Boolean`
   - `tenant_id: ID`

## Security

All operations are protected with appropriate authentication and authorization:
- Users can only manage real estate agencies within their tenant
- Only users with appropriate roles can perform CRUD operations
- All GraphQL operations use the `@auth` directive

### Requisitos de Autenticação e Autorização

Todas as chamadas GraphQL acima requerem autenticação via token OAuth. Além disso:

1. Para **listar e visualizar** imobiliárias (`realEstates`, `realEstateById`):
   - Qualquer usuário autenticado pode acessar dados da sua própria tenant
   - Administradores podem acessar todas as imobiliárias

2. Para **criar, atualizar ou excluir** imobiliárias (`createRealEstate`, `updateRealEstate`, `deleteRealEstate`):
   - Requer papel (role) `ROLE_SUPER_ADMIN` ou `ROLE_REAL_ESTATE_ADMIN`
   - Usuários não-admin não podem modificar imobiliárias
   - Usuários só podem modificar imobiliárias da sua própria tenant (exceto super-admin)

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
