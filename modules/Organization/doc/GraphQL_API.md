# Organization Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL queries and mutations available in the Organization module.

> **NEW ARCHITECTURE - IMPORTANT UPDATE:**
> The Organization module has been refactored to implement a **fully decoupled architecture**:
> 
> - **✅ Generic Organization Creation**: The `createOrganization` mutation creates generic organizations without type restrictions
> - **✅ Observer Pattern**: Other modules can extend Organization data through the Observer pattern
> - **✅ No Cross-Module Dependencies**: The Organization module is completely independent and doesn't reference other modules
> - **✅ Extension Data**: Modules can inject additional data into Organization queries through the `extensionData` field
> - **✅ Specialized Mutations**: For specialized organizations (like real estate), use the appropriate module's mutations
>
> **Key Changes:**
> - Removed `OrganizationType` enum - organizations are now generic
> - Removed `type` field from Organization schema
> - Removed `extensionData` field from Organization schema (handled dynamically via Observer pattern)
> - Organization mutations only handle core organization data
> - Address management integrated into Organization module

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Authentication](#authentication)
3. [Architecture Overview](#architecture-overview)
4. [Organization Queries](#organization-queries)
5. [Organization Mutations](#organization-mutations)
6. [Organization Member Operations](#organization-member-operations)
7. [Organization Address Operations](#organization-address-operations)
8. [Extension Data Pattern](#extension-data-pattern)
9. [Performance and Optimization](#performance-and-optimization)
10. [Module Integration Guide](#module-integration-guide)
11. [Testing and Validation](#testing-and-validation)
12. [Debugging and Troubleshooting](#debugging-and-troubleshooting)
13. [Error Handling](#error-handling)
14. [Examples](#examples)
15. [Migration Guide](#migration-guide)
16. [Best Practices](#best-practices)

## Executive Summary

### Quick Start

**For Generic Organizations:**
```bash
# Create generic organization
curl -X POST http://realestate.localhost/graphql \
  -H "Authorization: Bearer your_token" \
  -d '{"query": "mutation { createOrganization(input: {name: \"My Org\", email: \"contact@myorg.com\"}) { id name } }"}'
```

**For Specialized Organizations:**
```bash
# Create specialized organization (example for any module that extends Organization)
curl -X POST http://realestate.localhost/graphql \
  -H "Authorization: Bearer your_token" \
  -d '{"query": "mutation { createSpecializedOrganization(input: {name: \"My Specialized Org\", email: \"contact@specialized.com\", specializedField: \"value\"}) { id name specializedField } }"}'
```

**Query with Extension Data:**
```bash
# Query organization with extension data
curl -X POST http://realestate.localhost/graphql \
  -H "Authorization: Bearer your_token" \
  -d '{"query": "query { organization(id: \"1\") { id name extensionData } }"}'
```

### Key Features

- **🔄 Decoupled Architecture**: Modules don't depend on each other
- **🔌 Extension Data**: Dynamic data injection through Observer pattern
- **📊 Flexible Querying**: Get complete organization info in single query
- **🛡️ Type Safety**: Strong typing through GraphQL schema
- **📈 Performance**: Lazy loading and caching support
- **🧪 Testable**: Comprehensive testing patterns provided

### Architecture Decision

The Organization module uses a **schema-first, decoupled approach** where:
- Organization schema contains only **core fields**
- **No cross-module dependencies** in the schema
- **Extension data populated dynamically** via Observer pattern
- **Specialized mutations** handled by respective modules

## Authentication

All GraphQL operations in the Organization module require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer your_access_token_here
```

To obtain an access token, make a POST request to `/oauth/token`:

```bash
curl -X POST http://realestate.localhost/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "password",
    "client_id": "1",
    "client_secret": "your_client_secret",
    "username": "your_email@example.com",
    "password": "your_password"
  }'
```

## Architecture Overview

### Decoupled Module Design

The Organization module now implements a **fully decoupled architecture**:

```
┌─────────────────┐    Observer Pattern    ┌─────────────────┐
│   Organization  │◄──────────────────────│   Other Module  │
│     Module      │                       │   (Extension)   │
│                 │                       │                 │
│ - Generic CRUD  │                       │ - Extends Org   │
│ - Address Mgmt  │                       │ - Specific Data │
│ - Member Mgmt   │                       │ - Custom Fields │
│ - extensionData │                       │                 │
└─────────────────┘                       └─────────────────┘
```

### Extension Data Pattern

Other modules can inject data into Organization queries through the `extensionData` field:

```graphql
query {
  organization(id: "1") {
    id
    name
    email
    extensionData  # Contains data from any extending modules
  }
}
```

The `extensionData` field is a **dynamic field** that contains additional information from modules that extend Organization. This field is populated through the Observer pattern and is not stored in the Organization schema itself.

```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Example Organization",
      "email": "contact@example.com",
      "extensionData": {
        "specializedModule": {
          "id": "1",
          "specialized_field": "value",
          "another_field": "another_value"
        }
      }
    }
  }
}
```

## Organization Creation Strategies

### Strategy 1: Generic Organization Creation (Recommended for Generic Organizations)

Create a generic organization using the Organization module:

```graphql
mutation CreateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    id
    name
    fantasy_name
    cnpj
    description
    email
    phone
    website
    active
    created_at
    updated_at
  }
}
```

**Example Variables:**
```json
{
  "input": {
    "name": "Generic Organization",
    "fantasy_name": "Generic Org",
    "cnpj": "12345678901234",
    "description": "A generic organization",
    "email": "contact@generic.com",
    "phone": "+1-555-0123",
    "website": "https://generic.com",
    "active": true
  }
}
```

### Strategy 2: Specialized Organization Creation (Recommended for Specialized Organizations)

For specialized organizations, use the specific module's mutations. The Organization module itself doesn't provide specialized mutations - these are provided by other modules that extend Organization:

```graphql
# Example: Specialized Module Mutation (provided by other modules)
mutation CreateSpecializedOrganization($input: CreateSpecializedOrganizationInput!) {
  createSpecializedOrganization(input: $input) {
    id
    name
    cnpj
    specialized_field
    another_field
    # Organization fields delegated automatically
  }
}
```

**Benefits of Strategy 2:**
- ✅ Creates both Organization and specialized record in one transaction
- ✅ Handles validation for specialized fields
- ✅ Automatic relationship management
- ✅ Extension data automatically available in Organization queries through Observer pattern

**Example Variables (structure depends on the extending module):**
```json
{
  "input": {
    "name": "Specialized Organization",
    "fantasy_name": "Specialized Org",
    "cnpj": "12345678901234",
    "description": "A specialized organization",
    "email": "contact@specialized.com",
    "phone": "+55 11 99999-9999",
    "website": "https://specialized.com",
    "active": true,
    "specialized_field": "value",
    "another_field": "another_value"
  }
}
```

**cURL Example (depends on the extending module's endpoint):**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateSpecializedOrganization($input: CreateSpecializedOrganizationInput!) { createSpecializedOrganization(input: $input) { id name fantasy_name cnpj description email phone website active specialized_field another_field } }",
    "variables": {
      "input": {
        "name": "Specialized Organization",
        "fantasy_name": "Specialized Org",
        "cnpj": "12345678901234",
        "description": "A specialized organization",
        "email": "contact@specialized.com",
        "phone": "+55 11 99999-9999",
        "website": "https://specialized.com",
        "active": true,
        "specialized_field": "value",
        "another_field": "another_value"
      }
    }
  }'
```


## Organization Queries

### 1. Get Organization by ID

Retrieve a specific organization by its ID, including extension data from other modules.

**Query:**
```graphql
query GetOrganization($id: ID!) {
  organization(id: $id) {
    id
    name
    fantasy_name
    cnpj
    description
    email
    phone
    website
    active
    created_at
    updated_at
    extensionData  # Dynamic field populated by other modules via Observer pattern
    members {
      id
      user {
        id
        name
        email
      }
      role
      position
      is_active
      created_at
    }
    addresses {
      id
      street
      number
      complement
      neighborhood
      city
      state
      zip_code
      country
      type
      active
      created_at
      updated_at
    }
  }
}
```

**Variables:**
```json
{
  "id": "1"
}
```

**Example Response:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Example Organization",
      "fantasy_name": "Example Org",
      "cnpj": "12345678901234",
      "email": "contact@example.com",
      "extensionData": {
        "specializedModule": {
          "id": "1",
          "specialized_field": "value",
          "another_field": "another_value",
          "created_at": "2024-01-01T00:00:00Z",
          "updated_at": "2024-01-01T00:00:00Z"
        }
      }
    }
  }
}
```

> **Note**: The `extensionData` field is populated dynamically through the Observer pattern. It's not stored in the Organization table but injected by other modules when querying organizations.

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganization($id: ID!) { organization(id: $id) { id name fantasy_name cnpj description email phone website active created_at updated_at extensionData } }",
    "variables": {
      "id": "1"
    }
  }'
```

### 2. Get All Organizations

Retrieve a list of all organizations with pagination and extension data.

**Query:**
```graphql
query GetOrganizations($first: Int!, $page: Int) {
  organizations(first: $first, page: $page) {
    data {
      id
      name
      fantasy_name
      cnpj
      description
      email
      phone
      website
      active
      created_at
      updated_at
      extensionData
    }
    paginatorInfo {
      count
      currentPage
      firstItem
      hasMorePages
      lastItem
      lastPage
      perPage
      total
    }
  }
}
```

**Variables:**
```json
{
  "first": 10,
  "page": 1
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganizations($first: Int!, $page: Int) { organizations(first: $first, page: $page) { data { id name fantasy_name cnpj description email phone website active created_at updated_at extensionData } paginatorInfo { count currentPage firstItem hasMorePages lastItem lastPage perPage total } } }",
    "variables": {
      "first": 10,
      "page": 1
    }
  }'
```

### 3. Get Organization Address by ID

Retrieve a specific organization address by its ID.

**Query:**
```graphql
query GetOrganizationAddress($id: ID!) {
  organizationAddressById(id: $id) {
    id
    organization_id
    street
    number
    complement
    neighborhood
    city
    state
    zip_code
    country
    type
    active
    created_at
    updated_at
    organization {
      id
      name
      fantasy_name
      cnpj
      email
    }
  }
}
```

**Variables:**
```json
{
  "id": "1"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganizationAddress($id: ID!) { organizationAddressById(id: $id) { id organization_id street number complement neighborhood city state zip_code country type active created_at updated_at organization { id name fantasy_name cnpj email } } }",
    "variables": {
      "id": "1"
    }
  }'
```

### 4. Get Addresses by Organization ID

Retrieve all addresses for a specific organization.

**Query:**
```graphql
query GetAddressesByOrganization($organizationId: ID!) {
  addressesByOrganizationId(organizationId: $organizationId) {
    id
    organization_id
    street
    number
    complement
    neighborhood
    city
    state
    zip_code
    country
    type
    active
    created_at
    updated_at
  }
}
```

**Variables:**
```json
{
  "organizationId": "1"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetAddressesByOrganization($organizationId: ID!) { addressesByOrganizationId(organizationId: $organizationId) { id organization_id street number complement neighborhood city state zip_code country type active created_at updated_at } }",
    "variables": {
      "organizationId": "1"
    }
  }'
```

### 5. Get Members by Organization ID

Retrieve all members of a specific organization with optional filtering by role and active status.

**Authentication Required:** This query requires a valid authentication token via the Authorization header (Bearer token).

**Query:**
```graphql
query GetMembersByOrganization($organizationId: ID!, $active: Boolean, $role: String) {
  membersByOrganizationId(organizationId: $organizationId, active: $active, role: $role) {
    id
    role
    position
    isActive
    createdAt
    updatedAt
    user {
      id
      name
      email
    }
  }
}
```

**Variables:**
```json
{
  "organizationId": "1",
  "active": true,
  "role": "admin"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetMembersByOrganization($organizationId: ID!, $active: Boolean, $role: String) { membersByOrganizationId(organizationId: $organizationId, active: $active, role: $role) { id role position isActive createdAt updatedAt user { id name email } } }",
    "variables": {
      "organizationId": "1",
      "active": true,
      "role": "admin"
    }
  }'
```

## Extension Data Pattern

The Organization module implements a powerful **Extension Data Pattern** that allows other modules to inject additional data into Organization queries without creating dependencies.

### How Extension Data Works

1. **Observer Pattern**: Other modules register listeners for `OrganizationDataRequested` events
2. **Dynamic Injection**: When an Organization query is executed, modules can add data to the `extensionData` field
3. **No Dependencies**: The Organization module doesn't know about other modules
4. **Flexible Structure**: Extension data can contain any JSON structure

### Example: Module Extension

When querying an organization that has extension data from other modules, the `extensionData` field will automatically include:

```graphql
query {
  organization(id: "1") {
    id
    name
    email
    extensionData
  }
}
```

**Response:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Example Organization",
      "email": "contact@example.com",
      "extensionData": {
        "specializedModule": {
          "id": "1",
          "specialized_field": "value",
          "another_field": "another_value",
          "created_at": "2024-01-01T00:00:00Z",
          "updated_at": "2024-01-01T00:00:00Z"
        }
      }
    }
  }
}
```

### Benefits

✅ **Decoupled Architecture**: No dependencies between modules  
✅ **Extensible**: New modules can add data without modifying Organization  
✅ **Performance**: Data is loaded only when requested  
✅ **Flexible**: Any JSON structure can be added  
✅ **Maintainable**: Each module manages its own data  
✅ **Dynamic**: No schema changes needed in Organization module  

### Creating Extension Data Listeners

To create an extension data listener in your module:

```php
// In your module's ServiceProvider
$this->app['events']->listen(
    \Modules\Organization\Events\OrganizationDataRequested::class,
    \Modules\YourModule\Listeners\InjectYourModuleDataListener::class
);
```

```php
// In your listener class
class InjectYourModuleDataListener
{
    public function handle(OrganizationDataRequested $event): void
    {
        $organization = $event->organization;
        
        // Check if this organization has your module's data
        $yourData = YourModel::where('organization_id', $organization->id)->first();
        
        if ($yourData) {
            $event->addExtensionData('yourModule', [
                'id' => $yourData->id,
                'specific_field' => $yourData->specific_field,
                // ... other fields
            ]);
        }
    }
}
```

## Module Integration Guide

This section provides a complete guide for integrating new modules with the Organization module using the Observer pattern.

### Complete Module Integration Guide

#### Step 1: Create Your Module's Model
```php
// modules/YourModule/Models/YourModel.php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Organization\Models\Organization;

class YourModel extends Model
{
    protected $fillable = [
        'organization_id',
        'specific_field',
        // ... other fields
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
```

#### Step 2: Create Migration
```php
// modules/YourModule/Database/Migrations/create_your_models_table.php
Schema::create('your_models', function (Blueprint $table) {
    $table->id();
    $table->foreignId('organization_id')->constrained()->onDelete('cascade');
    $table->string('specific_field');
    // ... other fields
    $table->timestamps();
});
```

#### Step 3: Create Service Provider
```php
// modules/YourModule/Providers/YourModuleServiceProvider.php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Organization\Events\OrganizationDataRequested;
use Modules\YourModule\Listeners\InjectYourModuleDataListener;

class YourModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register extension data listener
        $this->app['events']->listen(
            OrganizationDataRequested::class,
            InjectYourModuleDataListener::class
        );
    }
}
```

#### Step 4: Create Extension Data Listener
```php
// modules/YourModule/Listeners/InjectYourModuleDataListener.php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Listeners;

use Modules\Organization\Events\OrganizationDataRequested;
use Modules\YourModule\Models\YourModel;

class InjectYourModuleDataListener
{
    public function handle(OrganizationDataRequested $event): void
    {
        $organization = $event->organization;
        
        $yourData = YourModel::where('organization_id', $organization->id)->first();
        
        if ($yourData) {
            $event->addExtensionData('yourModule', [
                'id' => $yourData->id,
                'specific_field' => $yourData->specific_field,
                'created_at' => $yourData->created_at->toISOString(),
                'updated_at' => $yourData->updated_at->toISOString(),
            ]);
        }
    }
}
```

#### Step 5: Create GraphQL Schema
```graphql
# modules/YourModule/GraphQL/schema.graphql
extend type Query {
    yourModel(id: ID! @eq): YourModel @find
    yourModels: [YourModel!]! @all
}

extend type Mutation {
    createYourModel(input: CreateYourModelInput!): YourModel!
    updateYourModel(id: ID!, input: UpdateYourModelInput!): YourModel!
    deleteYourModel(id: ID!): YourModel!
}

type YourModel {
    id: ID!
    organization_id: ID!
    specific_field: String!
    created_at: DateTime!
    updated_at: DateTime!
    
    # Relationship
    organization: Organization! @belongsTo
}

input CreateYourModelInput {
    name: String!
    fantasy_name: String
    cnpj: String
    description: String
    email: String!
    phone: String
    website: String
    active: Boolean = true
    specific_field: String!
}

input UpdateYourModelInput {
    name: String
    fantasy_name: String
    description: String
    email: String
    phone: String
    website: String
    active: Boolean
    specific_field: String
}
```

#### Step 6: Create GraphQL Resolver
```php
// modules/YourModule/GraphQL/Mutations/CreateYourModelResolver.php
<?php

declare(strict_types=1);

namespace Modules\YourModule\GraphQL\Mutations;

use Modules\Organization\Models\Organization;
use Modules\YourModule\Models\YourModel;

class CreateYourModelResolver
{
    public function __invoke($rootValue, array $args): YourModel
    {
        $input = $args['input'];
        
        // Extract organization data
        $organizationData = array_intersect_key($input, array_flip([
            'name', 'fantasy_name', 'cnpj', 'description', 'email', 'phone', 'website', 'active'
        ]));
        
        // Create organization
        $organization = Organization::create($organizationData);
        
        // Extract your module specific data
        $yourData = array_intersect_key($input, array_flip([
            'specific_field'
        ]));
        $yourData['organization_id'] = $organization->id;
        
        // Create your model
        $yourModel = YourModel::create($yourData);
        
        return $yourModel;
    }
}
```

#### Step 7: Test Extension Data
```php
// Test that extension data is populated
$response = $this->postGraphQL('
    query GetOrganization($id: ID!) {
        organization(id: $id) {
            id
            name
            extensionData
        }
    }
', ['id' => $organization->id]);

$this->assertArrayHasKey('yourModule', $response['data']['organization']['extensionData']);
```

This complete integration allows your module to:
- ✅ Create specialized organizations
- ✅ Inject extension data into Organization queries
- ✅ Maintain complete decoupling
- ✅ Handle both Organization and specialized fields

## Organization Mutations

### 1. Create Organization

Create a new generic organization.

**Mutation:**
```graphql
mutation CreateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    id
    name
    fantasy_name
    cnpj
    description
    email
    phone
    website
    active
    created_at
    updated_at
    extensionData
  }
}
```

**Variables:**
```json
{
  "input": {
    "name": "New Organization",
    "fantasy_name": "Fancy Org Name",
    "cnpj": "12345678901234",
    "description": "This is a new generic organization",
    "email": "contact@neworg.com",
    "phone": "+55 11 99999-9999",
    "website": "https://neworg.com",
    "active": true
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { id name fantasy_name cnpj description email phone website active created_at updated_at extensionData } }",
    "variables": {
      "input": {
        "name": "New Organization",
        "fantasy_name": "Fancy Org Name",
        "cnpj": "12345678901234",
        "description": "This is a new generic organization",
        "email": "contact@neworg.com",
        "phone": "+55 11 99999-9999",
        "website": "https://neworg.com",
        "active": true
      }
    }
  }'
```

> **Note**: This creates a **generic organization only**. If you need a specialized organization, consider using the specific module's mutations (e.g., from modules that extend Organization) which will create both the organization and the specialized record in a single transaction.

### 2. Update Organization

Update an existing organization's details.

**Mutation:**
```graphql
mutation UpdateOrganization($id: ID!, $input: UpdateOrganizationInput!) {
  updateOrganization(id: $id, input: $input) {
    id
    name
    fantasy_name
    cnpj
    description
    email
    phone
    website
    active
    created_at
    updated_at
  }
}
```

**Variables:**
```json
{
  "id": "3",
  "input": {
    "name": "Updated Organization",
    "description": "This organization has been updated",
    "phone": "+55 11 99999-8888",
    "website": "https://updated-org.com",
    "active": true
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation UpdateOrganization($id: ID!, $input: UpdateOrganizationInput!) { updateOrganization(id: $id, input: $input) { id name fantasy_name description email phone website active created_at updated_at } }",
    "variables": {
      "id": "3",
      "input": {
        "name": "Updated Organization",
        "description": "This organization has been updated",
        "phone": "+55 11 99999-8888",
        "website": "https://updated-org.com"
      }
    }
  }'
```

### 3. Delete Organization

Delete an organization by its ID.

**Mutation:**
```graphql
mutation DeleteOrganization($id: ID!) {
  deleteOrganization(id: $id) {
    id
    name
    fantasy_name
    cnpj
    email
  }
}
```

**Variables:**
```json
{
  "id": "3"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation DeleteOrganization($id: ID!) { deleteOrganization(id: $id) { id name fantasy_name cnpj email } }",
    "variables": {
      "id": "3"
    }
  }'
```

> **Important Note on JSON Format**: 
> When sending GraphQL requests, ensure your JSON is properly formatted. Common errors include:
> - Missing quotation marks around string values
> - Missing closing braces or brackets
> - Extra commas after the last item in an object or array
>
> Example of a properly formatted request body:
> ```json
> {
>   "query": "mutation DeleteOrganization($id: ID!) { deleteOrganization(id: $id) { id name } }",
>   "variables": {
>     "id": "3"
>   }
> }
> ```

## Organization Member Operations

### 1. Add Organization Member

Add a user as a member to an organization.

**Mutation:**
```graphql
mutation AddOrganizationMember($organizationId: ID!, $userId: ID!, $role: String!, $position: String, $joinedAt: DateTime) {
  addOrganizationMember(
    organizationId: $organizationId,
    userId: $userId,
    role: $role,
    position: $position,
    joinedAt: $joinedAt
  )
}
```

**Variables:**
```json
{
  "organizationId": "1",
  "userId": "2",
  "role": "manager",
  "position": "Sales Manager",
  "joinedAt": "2023-01-15T00:00:00Z"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation AddOrganizationMember($organizationId: ID!, $userId: ID!, $role: String!, $position: String, $joinedAt: DateTime) { addOrganizationMember(organizationId: $organizationId, userId: $userId, role: $role, position: $position, joinedAt: $joinedAt) }",
    "variables": {
      "organizationId": "1",
      "userId": "2",
      "role": "manager",
      "position": "Sales Manager",
      "joinedAt": "2023-01-15T00:00:00Z"
    }
  }'
```

### 2. Update Organization Member

Update the details of an existing organization member.

**Mutation:**
```graphql
mutation UpdateOrganizationMember($organizationId: ID!, $userId: ID!, $role: String, $position: String, $isActive: Boolean) {
  updateOrganizationMember(
    organizationId: $organizationId,
    userId: $userId,
    role: $role,
    position: $position,
    isActive: $isActive
  )
}
```

**Variables:**
```json
{
  "organizationId": "1",
  "userId": "2",
  "role": "admin",
  "position": "Director",
  "isActive": true
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation UpdateOrganizationMember($organizationId: ID!, $userId: ID!, $role: String, $position: String, $isActive: Boolean) { updateOrganizationMember(organizationId: $organizationId, userId: $userId, role: $role, position: $position, isActive: $isActive) }",
    "variables": {
      "organizationId": "1",
      "userId": "2",
      "role": "admin",
      "position": "Director",
      "isActive": true
    }
  }'
```

### 3. Remove Organization Member

Remove a user from an organization.

**Mutation:**
```graphql
mutation RemoveOrganizationMember($organizationId: ID!, $userId: ID!) {
  removeOrganizationMember(
    organizationId: $organizationId,
    userId: $userId
  )
}
```

**Variables:**
```json
{
  "organizationId": "1",
  "userId": "2"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation RemoveOrganizationMember($organizationId: ID!, $userId: ID!) { removeOrganizationMember(organizationId: $organizationId, userId: $userId) }",
    "variables": {
      "organizationId": "1",
      "userId": "2"
    }
  }'
```

## Organization Address Operations

### 1. Create Organization Address

Create a new address for an organization.

**Mutation:**
```graphql
mutation CreateOrganizationAddress($organizationId: ID!, $input: OrganizationAddressInput!) {
  createOrganizationAddress(
    organizationId: $organizationId,
    input: $input
  ) {
    id
    street
    number
    complement
    neighborhood
    city
    state
    zip_code
    country
    type
    active
    created_at
    updated_at
  }
}
```

**Variables:**
```json
{
  "organizationId": "1",
  "input": {
    "type": "headquarters",
    "street": "Main Street",
    "number": "123",
    "complement": "Suite 45",
    "neighborhood": "Downtown",
    "city": "New York",
    "state": "NY",
    "zipCode": "10001",
    "country": "US"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganizationAddress($organizationId: ID!, $input: OrganizationAddressInput!) { createOrganizationAddress(organizationId: $organizationId, input: $input) { id street number complement neighborhood city state zip_code country type active created_at updated_at } }",
    "variables": {
      "organizationId": "1",
      "input": {
        "type": "headquarters",
        "street": "Main Street",
        "number": "123",
        "complement": "Suite 45",
        "neighborhood": "Downtown",
        "city": "New York",
        "state": "NY",
        "zipCode": "10001",
        "country": "US"
      }
    }
  }'
```

### 2. Update Organization Address

Update an existing organization address.

**Mutation:**
```graphql
mutation UpdateOrganizationAddress($id: ID!, $input: UpdateOrganizationAddressInput!) {
  updateOrganizationAddress(id: $id, input: $input) {
    id
    street
    number
    complement
    neighborhood
    city
    state
    zip_code
    country
    type
    active
    created_at
    updated_at
  }
}
```

**Variables:**
```json
{
  "id": "1",
  "input": {
    "street": "Broadway",
    "number": "456",
    "complement": "Floor 10",
    "neighborhood": "Theater District",
    "city": "New York",
    "state": "NY",
    "zipCode": "10019",
    "active": true
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation UpdateOrganizationAddress($id: ID!, $input: UpdateOrganizationAddressInput!) { updateOrganizationAddress(id: $id, input: $input) { id street number complement neighborhood city state zip_code country type active created_at updated_at } }",
    "variables": {
      "id": "1",
      "input": {
        "street": "Broadway",
        "number": "456",
        "complement": "Floor 10",
        "neighborhood": "Theater District",
        "city": "New York",
        "state": "NY",
        "zipCode": "10019",
        "active": true
      }
    }
  }'
```

### 3. Delete Organization Address

Delete an organization address by ID.

**Mutation:**
```graphql
mutation DeleteOrganizationAddress($id: ID!) {
  deleteOrganizationAddress(id: $id) {
    id
    street
    number
    complement
    neighborhood
    city
    zip_code
    country
    type
  }
}
```

**Variables:**
```json
{
  "id": "1"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation DeleteOrganizationAddress($id: ID!) { deleteOrganizationAddress(id: $id) { id street number complement neighborhood city state zip_code country type } }",
    "variables": {
      "id": "1"
    }
  }'
```

## Error Handling

When using the GraphQL API, you may encounter various error scenarios. Here are some common errors and how to handle them:

### 1. Authentication Errors

If your authentication token is missing, invalid, or expired, you'll receive an error like:

```json
{
  "errors": [
    {
      "message": "Unauthenticated.",
      "extensions": {
        "category": "authentication"
      }
    }
  ]
}
```

**Solution**: Refresh your access token by making a new request to the `/oauth/token` endpoint.

### 2. Validation Errors

When input data fails validation, you'll receive detailed errors:

```json
{
  "errors": [
    {
      "message": "Validation failed for the field [createOrganizationAddress].",
      "extensions": {
        "validation": {
          "input.street": [
            "The street field is required."
          ],
          "input.zipCode": [
            "The zip code must be 8 characters."
          ]
        },
        "category": "validation"
      }
    }
  ]
}
```

**Solution**: Check the error message and adjust your input data accordingly.

### 3. Not Found Errors

When requesting a resource that doesn't exist:

```json
{
  "errors": [
    {
      "message": "Organization with ID 999 not found",
      "extensions": {
        "category": "not_found"
      }
    }
  ]
}
```

**Solution**: Verify the ID you're using and check if the resource exists.

## Examples

### Complete Organization Flow Example

Here's an example of a complete workflow to create an organization, add addresses, and add members:

1. First, create the organization:

```graphql
mutation CreateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    id
    name
    fantasy_name
    description
    email
    phone
    website
    active
  }
}
```

2. Update the organization if needed:

```graphql
mutation UpdateOrganization($id: ID!, $input: UpdateOrganizationInput!) {
  updateOrganization(id: $id, input: $input) {
    id
    name
    fantasy_name
    description
    email
    phone
    website
    active
    created_at
    updated_at
  }
}
```

3. Delete the organization if needed:

```graphql
mutation DeleteOrganization($id: ID!) {
  deleteOrganization(id: $id) {
    id
    name
    fantasy_name
    cnpj
    email
  }
}
```

4. Add an address to the organization:

```graphql
mutation CreateOrganizationAddress($organizationId: ID!, $input: OrganizationAddressInput!) {
  createOrganizationAddress(
    organizationId: $organizationId,
    input: $input
  ) {
    id
    street
    city
    state
    zip_code
    country
    type
  }
}
```

5. Add a member to the organization:

```graphql
mutation AddOrganizationMember($organizationId: ID!, $userId: ID!, $role: String!, $position: String) {
  addOrganizationMember(
    organizationId: $organizationId,
    userId: $userId,
    role: $role,
    position: $position
  )
}
```

## Practical Examples - New Architecture

### Example 1: Creating a Specialized Organization

**Step 1:** Use a specialized module to create a specialized organization:

```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateSpecializedOrganization($input: CreateSpecializedOrganizationInput!) { createSpecializedOrganization(input: $input) { id name cnpj specialized_field another_field } }",
    "variables": {
      "input": {
        "name": "Specialized Organization",
        "fantasy_name": "Special Org",
        "cnpj": "12345678901234",
        "email": "contact@specialized.com",
        "phone": "+55 11 99999-9999",
        "specialized_field": "value",
        "another_field": "another_value"
      }
    }
  }'
```

**Step 2:** Query the organization with extension data:

```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganization($id: ID!) { organization(id: $id) { id name fantasy_name cnpj email extensionData } }",
    "variables": {
      "id": "1"
    }
  }'
```

**Response:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Specialized Organization",
      "fantasy_name": "Special Org",
      "cnpj": "12345678901234",
      "email": "contact@specialized.com",
      "extensionData": {
        "specializedModule": {
          "id": "1",
          "specialized_field": "value",
          "another_field": "another_value",
          "created_at": "2024-01-01T00:00:00Z",
          "updated_at": "2024-01-01T00:00:00Z"
        }
      }
    }
  }
}
```

### Example 2: Creating a Generic Organization

**Step 1:** Use the Organization module for generic organizations:

```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { id name fantasy_name cnpj email active } }",
    "variables": {
      "input": {
        "name": "Consultoria ABC",
        "fantasy_name": "ABC Consulting",
        "cnpj": "98765432109876",
        "email": "contato@abc.com",
        "phone": "+55 11 88888-8888",
        "description": "Consultoria em negócios"
      }
    }
  }'
```

### Example 3: Listing Organizations with Extension Data

```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganizations($first: Int!) { organizations(first: $first) { data { id name fantasy_name cnpj email extensionData } } }",
    "variables": {
      "first": 10
    }
  }'
```

**Response:**
```json
{
  "data": {
    "organizations": {
      "data": [
        {
          "id": "1",
          "name": "Specialized Organization",
          "fantasy_name": "Special Org",
          "cnpj": "12345678901234",
          "email": "contact@specialized.com",
          "extensionData": {
            "specializedModule": {
              "id": "1",
              "specialized_field": "value",
              "another_field": "another_value"
            }
          }
        },
        {
          "id": "2",
          "name": "Consultoria ABC",
          "fantasy_name": "ABC Consulting",
          "cnpj": "98765432109876",
          "email": "contato@abc.com",
          "extensionData": null
        }
      ]
    }
  }
}
```

## Migration Guide

This section provides a comprehensive migration guide from the old architecture to the new decoupled architecture.

## Migration from Old Architecture

If you're migrating from the old architecture with `OrganizationType` enum:

### Old Way (Deprecated)
```graphql
# ❌ These fields no longer exist in the schema
mutation CreateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    organization_type  # Field completely removed
    type               # Field never existed
    extensionData      # Field removed from schema (handled dynamically)
  }
}
```

### New Way (Current)
```graphql
# ✅ Use specific module mutations for specialized organizations
mutation CreateSpecializedOrganization($input: CreateSpecializedOrganizationInput!) {
  createSpecializedOrganization(input: $input) {
    id
    name
    specialized_field  # Specific fields handled by specialized modules
  }
}

# ✅ Or create generic organizations  
mutation CreateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    id
    name
    # extensionData populated automatically via Observer pattern when querying
  }
}

# ✅ Query organizations with extension data
query GetOrganization($id: ID!) {
  organization(id: $id) {
    id
    name
    extensionData  # Populated automatically by other modules
  }
}
```

## Best Practices

1. **✅ Use specialized mutations** for specific organization types (provided by specialized modules)
2. **✅ Use generic mutations** only for truly generic organizations
3. **✅ Always include extensionData** in queries to get complete information
4. **✅ Handle null extensionData** gracefully in your frontend
5. **✅ Use the Observer pattern** when creating new module extensions
6. **✅ Test extension data** by querying organizations after specialized creation
7. **✅ Validate specialized fields** in their respective modules, not in Organization
8. **✅ Keep Organization module generic** - avoid adding specialized fields to the schema

### Common Patterns

#### Creating Specialized Organizations
```graphql
# 1. Create via specialized module
mutation CreateSpecializedOrganization($input: CreateSpecializedOrganizationInput!) {
  createSpecializedOrganization(input: $input) {
    id
    name
    specialized_field
    another_field
  }
}

# 2. Query with extension data
query GetOrganization($id: ID!) {
  organization(id: $id) {
    id
    name
    extensionData
  }
}
```

#### Listing Organizations with Extensions
```graphql
query ListOrganizations {
  organizations(first: 10) {
    data {
      id
      name
      extensionData
    }
  }
}
```

#### Handling Extension Data in Frontend
```javascript
// Example JavaScript handling
const organization = query.organization;
const specializedData = organization.extensionData?.specializedModule;

if (specializedData) {
  console.log('Specialized Field:', specializedData.specialized_field);
  console.log('Another Field:', specializedData.another_field);
}
```

## Performance and Optimization

### Extension Data Loading

The Observer pattern for extension data provides several performance benefits:

1. **Lazy Loading**: Extension data is only loaded when requested
2. **Selective Loading**: Only modules with actual data contribute to extensionData
3. **Cacheable**: Results can be cached at the GraphQL level

### Best Practices for Performance

#### 1. Use Specific Queries
```graphql
# ✅ Good - Only request what you need
query GetOrganization($id: ID!) {
  organization(id: $id) {
    id
    name
    email
  }
}

# ❌ Avoid - Loading extension data when not needed
query GetOrganization($id: ID!) {
  organization(id: $id) {
    id
    name
    email
    extensionData  # Only include if you need it
  }
}
```

#### 2. Batch Organization Queries
```graphql
# ✅ Good - Batch loading
query GetOrganizations {
  organizations(first: 10) {
    data {
      id
      name
      extensionData
    }
  }
}
```

#### 3. Optimize Listeners
```php
// ✅ Good - Efficient listener
public function handle(OrganizationDataRequested $event): void
{
    $organizationId = $event->organization->id;
    
    // Use efficient query
    $specializedData = SpecializedModel::select(['id', 'specialized_field', 'another_field', 'created_at', 'updated_at'])
        ->where('organization_id', $organizationId)
        ->first();
    
    if ($specializedData) {
        $event->addExtensionData('specializedModule', $specializedData->toArray());
    }
}
```

### Caching Strategies

Consider implementing caching for extension data:

```php
// Example caching in listener
public function handle(OrganizationDataRequested $event): void
{
    $organizationId = $event->organization->id;
    $cacheKey = "specialized_extension_{$organizationId}";
    
    $specializedData = Cache::remember($cacheKey, 3600, function () use ($organizationId) {
        return SpecializedModel::where('organization_id', $organizationId)->first();
    });
    
    if ($specializedData) {
        $event->addExtensionData('specializedModule', $specializedData->toArray());
    }
}
```

## Debugging and Troubleshooting

### Extension Data Not Appearing

If `extensionData` is not appearing in your queries:

1. **Check if Observer is registered**:
   ```bash
   # In your module's ServiceProvider
   $this->app['events']->listen(
       \Modules\Organization\Events\OrganizationDataRequested::class,
       \Modules\YourModule\Listeners\InjectYourModuleDataListener::class
   );
   ```

2. **Verify the organization has specialized data**:
   ```sql
   -- Check if specialized record exists (replace with your table name)
   SELECT * FROM specialized_models WHERE organization_id = 1;
   ```

3. **Test the listener directly**:
   ```php
   // In tinker or test
   $organization = Organization::find(1);
   $event = new OrganizationDataRequested($organization);
   event($event);
   dd($event->getExtensionData());
   ```

### Common Issues

#### Issue: `extensionData` is always null
**Solution**: Ensure your module's listener is properly registered and the organization has related data.

#### Issue: Query returns "Field 'extensionData' doesn't exist"
**Solution**: The `extensionData` field is dynamically resolved. Make sure you're querying an organization that exists.

#### Issue: Extension data is incomplete
**Solution**: Check if your listener is properly handling the `OrganizationDataRequested` event and calling `$event->addExtensionData()`.

### Testing Extension Data

```php
// Example test
public function testExtensionDataIsPopulated()
{
    $organization = Organization::factory()->create();
    $specializedModel = SpecializedModel::factory()->create(['organization_id' => $organization->id]);
    
    $query = '
        query GetOrganization($id: ID!) {
            organization(id: $id) {
                id
                name
                extensionData
            }
        }
    ';
    
    $response = $this->postGraphQL($query, ['id' => $organization->id]);
    
    $this->assertArrayHasKey('extensionData', $response['data']['organization']);
    $this->assertArrayHasKey('specializedModule', $response['data']['organization']['extensionData']);
}
```

## Testing and Validation

This section covers comprehensive testing strategies for the Organization module and extension data.

### Testing Guidelines for Extension Data

#### Unit Tests for Listeners
```php
// Test the listener directly
public function testListenerInjectsExtensionData(): void
{
    $organization = Organization::factory()->create();
    $specializedModel = SpecializedModel::factory()->create(['organization_id' => $organization->id]);
    
    $event = new OrganizationDataRequested($organization);
    $listener = new InjectSpecializedDataListener();
    
    $listener->handle($event);
    
    $extensionData = $event->getExtensionData();
    $this->assertArrayHasKey('specializedModule', $extensionData);
    $this->assertEquals($specializedModel->id, $extensionData['specializedModule']['id']);
}
```

#### Integration Tests
```php
// Test the complete GraphQL flow
public function testOrganizationQueryWithExtensionData(): void
{
    $organization = Organization::factory()->create();
    $specializedModel = SpecializedModel::factory()->create([
        'organization_id' => $organization->id,
        'specialized_field' => 'value',
        'another_field' => 'another_value'
    ]);
    
    $response = $this->postGraphQL('
        query GetOrganization($id: ID!) {
            organization(id: $id) {
                id
                name
                extensionData
            }
        }
    ', ['id' => $organization->id]);
    
    $response->assertJson([
        'data' => [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'extensionData' => [
                    'specializedModule' => [
                        'id' => $specializedModel->id,
                        'specialized_field' => 'value',
                        'another_field' => 'another_value'
                    ]
                ]
            ]
        ]
    ]);
}
```

#### Test for No Extension Data
```php
// Test organization without extension data
public function testOrganizationWithoutExtensionData(): void
{
    $organization = Organization::factory()->create();
    
    $response = $this->postGraphQL('
        query GetOrganization($id: ID!) {
            organization(id: $id) {
                id
                name
                extensionData
            }
        }
    ', ['id' => $organization->id]);
    
    $response->assertJson([
        'data' => [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'extensionData' => null
            ]
        ]
    ]);
}
```

### Validation Best Practices

#### 1. Validate in Specialized Modules
```php
// In CreateSpecializedOrganizationResolver (example from a specialized module)
public function __invoke($rootValue, array $args): SpecializedModel
{
    $input = $args['input'];
    
    // Validate specialized field format
    if (!preg_match('/^[A-Z]-\d{5}$/', $input['specialized_field'])) {
        throw new \GraphQL\Error\Error('Invalid specialized field format');
    }
    
    // Continue with creation...
}
```

#### 2. Use Laravel Validation
```php
// Create validation rules
$rules = [
    'specialized_field' => 'required|string|regex:/^[A-Z]-\d{5}$/',
    'another_field' => 'required|string|size:15',
    'name' => 'required|string|max:255',
    'cnpj' => 'required|string|size:14',
    'email' => 'required|email'
];

$validator = Validator::make($input, $rules);

if ($validator->fails()) {
    throw new ValidationException($validator);
}
```

#### 3. Handle Validation Errors
```php
// Return structured validation errors
try {
    $validator->validate();
} catch (ValidationException $e) {
    throw new \GraphQL\Error\Error(
        'Validation failed: ' . implode(', ', $e->errors()),
        null,
        null,
        null,
        null,
        null,
        ['validation' => $e->errors()]
    );
}
```

---

**Documentation updated:** July 6, 2025  
**Architecture version:** 2.0 (Decoupled)  
**Schema version:** Organization v2.0 (No type field, no extensionData field, dynamic Observer pattern)

## Key Architecture Changes

### Version 2.0 Changes (Current)
- ✅ **Removed** `OrganizationType` enum
- ✅ **Removed** `type` field from Organization schema
- ✅ **Removed** `extensionData` field from Organization schema
- ✅ **Added** dynamic extension data via Observer pattern
- ✅ **Improved** decoupling between modules
- ✅ **Enhanced** specialization through dedicated module mutations

### Breaking Changes from v1.x
- `organization_type` field removed from all mutations and queries
- `extensionData` field removed from schema (still available in queries via Observer)
- Create organization mutations now only handle core organization fields
- Specialized organization creation must use respective module mutations

### Migration Steps
1. Replace `createOrganization` calls with specialized mutations where needed
2. Remove `organization_type` from mutation variables
3. Update queries to handle dynamic `extensionData` field
4. Test extension data population through Observer pattern
