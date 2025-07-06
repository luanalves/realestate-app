# RealEstate Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL mutations available in the RealEstate module.

**Important**: The RealEstate module integrates with the Organization module's extension system. For **reading** real estate data, use the Organization module's queries with `extensionData`. This document focuses on **creating and updating** real estate agencies.

## Table of Contents

1. [Introduction](#introduction)
2. [Authentication](#authentication)
3. [Integration with Organization Module](#integration-with-organization-module)
4. [RealEstate Mutations](#realestate-mutations)
5. [Error Handling](#error-handling)
6. [Examples](#examples)

## Introduction

The RealEstate module provides GraphQL mutations for managing real estate agencies within the system. It allows authorized users to create and update real estate entities.

**Reading Data**: For reading real estate data, use the Organization module's queries with `extensionData` field, which provides complete organization + real estate information in a single query.

**Writing Data**: Use the mutations documented here to create and update real estate agencies.

**Deleting Data**: To delete a real estate agency, delete the corresponding Organization record using the Organization module's mutations. The real estate record will be automatically removed due to CASCADE foreign key constraints.

## Authentication

**Important**: All operations in the RealEstate module require authentication. The system uses Laravel Passport for OAuth token-based authentication.

**Note**: Due to the architecture design, RealEstate entities delegate most of their fields to the Organization model. When querying RealEstate data, you should access organization-specific fields (name, email, etc.) through the `organization` relationship rather than directly on the RealEstate type.

### Required Permissions

Operations within the RealEstate module require specific permissions:

- **Read operations**: Any authenticated user can read real estate data
- **Write operations (create, update)**: Restricted to users with the following roles:
  - `super_admin`
  - `real_estate_admin`
- **Delete operations**: Use the Organization module's delete mutation (requires appropriate permissions)

### Multi-Tenant Access Control

The RealEstate module implements multi-tenant access control:

- `super_admin` users can access all real estate entities
- Other users can only access real estate entities within their assigned tenant

## Integration with Organization Module

The RealEstate module integrates with the Organization module's extension system, allowing you to get complete organization and real estate data in a single query.

### Getting Organization Data with Real Estate Extensions

**Query:**
```graphql
query GetOrganizationWithRealEstate($id: ID!) {
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
    addresses {
      id
      street
      number
      city
      state
      zip_code
      country
    }
    extensionData  # Contains real estate specific data
  }
}
```

**Variables:**
```json
{
  "id": "4"
}
```

**cURL Example:**
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "query": "query GetOrganizationWithRealEstate($id: ID!) { organization(id: $id) { id name fantasy_name cnpj description email phone website active addresses { id street number city state zip_code country } extensionData } }",
    "variables": {
      "id": "4"
    }
  }' \
  "http://realestate.localhost/graphql"
```

**Response:**
```json
{
  "data": {
    "organization": {
      "id": "4",
      "name": "Imobiliária ABC",
      "fantasy_name": null,
      "cnpj": "98765432000123",
      "description": "Segunda imobiliária para testes",
      "email": "contato@imobiliariaabc.com",
      "phone": "+55 11 99999-8888",
      "website": "https://imobiliariaabc.com",
      "active": true,
      "addresses": [],
      "extensionData": "{\"realEstate\":{\"id\":2,\"creci\":\"J-54321\",\"state_registration\":\"987.654.321.000\",\"created_at\":\"2025-07-04T23:09:15.000000Z\",\"updated_at\":\"2025-07-04T23:09:15.000000Z\"}}"
    }
  }
}
```

**Note**: The `extensionData` field contains a JSON string with real estate specific data. This includes:
- `id`: Real estate record ID
- `creci`: CRECI registration number
- `state_registration`: State registration number
- `created_at`: Real estate record creation date
- `updated_at`: Real estate record last update date

### Benefits of Using Organization Extension System

1. **Complete Data**: Get both organization and real estate data in one query
2. **Addresses**: Access organization addresses in the same query
3. **Flexibility**: The system is extensible for other organization types
4. **Performance**: Efficient data loading with proper relationships

### Recommended Approach: Organization Extension System

**The RealEstate module integrates with the Organization module's extension system**. This is the **recommended approach** for accessing real estate data:

**Use Organization Extension System for:**
- Getting individual real estate data with complete organization information
- Listing real estate agencies (query `organizations` with `extensionData`)
- Accessing organization addresses
- Building user interfaces that show organization information
- Supporting multiple organization types

**Use Direct RealEstate Mutations for:**
- Creating new real estate agencies
- Updating existing real estate agencies

**Note**: To delete a real estate agency, delete the corresponding Organization record. The real estate record will be automatically removed due to CASCADE foreign key constraint.

## RealEstate Mutations

The RealEstate module provides GraphQL mutations for creating and updating real estate agencies. These operations work in conjunction with the Organization module to manage the complete real estate data structure.

**Important**: All mutations require authentication and appropriate permissions. The mutations automatically handle both Organization and RealEstate data creation/updates.

**Note**: For deleting real estate agencies, use the Organization module's delete mutation, which will automatically remove the real estate record due to CASCADE foreign key constraints.

### 1. Create Real Estate

Create a new real estate agency.

**Authentication Required:** Yes (super_admin or real_estate_admin roles only)

**Mutation:**
```graphql
mutation CreateRealEstate($input: CreateRealEstateInput!) {
  createRealEstate(input: $input) {
    id
    organization_id
    creci
    state_registration
    created_at
    updated_at
    organization {
      name
      fantasy_name
      cnpj
      description
      email
      phone
      website
      active
    }
  }
}
```

**Variables:**
```json
{
  "input": {
    "name": "Nova Imobiliária",
    "fantasy_name": "Nova Imob",
    "cnpj": "98765432109876",
    "description": "Descrição da nova imobiliária",
    "email": "contato@novaimob.com",
    "phone": "11988888888",
    "website": "https://www.novaimob.com",
    "creci": "J-54321",
    "state_registration": "987654321",
    "active": true,
    "address": {
      "street": "Avenida Principal",
      "number": "1000",
      "complement": "Sala 101",
      "neighborhood": "Centro",
      "city": "São Paulo",
      "state": "SP",
      "zip_code": "01000-000",
      "country": "BR"
    }
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." \
  -d '{
    "query": "mutation CreateRealEstate($input: CreateRealEstateInput!) { createRealEstate(input: $input) { id organization_id creci state_registration created_at updated_at organization { name fantasy_name cnpj description email phone website active } } }",
    "variables": { "input": { "name": "Nova Imobiliária", "fantasy_name": "Nova Imob", "cnpj": "98765432109876", "description": "Descrição da nova imobiliária", "email": "contato@novaimob.com", "phone": "11988888888", "website": "https://www.novaimob.com", "creci": "J-54321", "state_registration": "987654321", "active": true, "address": { "street": "Avenida Principal", "number": "1000", "complement": "Sala 101", "neighborhood": "Centro", "city": "São Paulo", "state": "SP", "zip_code": "01000-000", "country": "BR" } } }
  }'
```

**Technical Implementation:**

The `createRealEstate` mutation is implemented in the `CreateRealEstateResolver` class, which:

1. Validates the user has permission to create real estate entities
2. Sets the tenant_id based on the authenticated user (for non-super_admin users)
3. Creates both the Organization and RealEstate records in a single transaction
4. Creates the address if provided
5. Returns the newly created real estate entity

### 2. Update Real Estate

Update an existing real estate agency.

**Authentication Required:** Yes (super_admin or real_estate_admin roles only)

**Mutation:**
```graphql
mutation UpdateRealEstate($id: ID!, $input: UpdateRealEstateInput!) {
  updateRealEstate(id: $id, input: $input) {
    id
    organization_id
    creci
    state_registration
    created_at
    updated_at
    organization {
      name
      fantasy_name
      cnpj
      description
      email
      phone
      website
      active
    }
  }
}
```

**Variables:**
```json
{
  "id": "1",
  "input": {
    "name": "Imobiliária Atualizada",
    "fantasy_name": "Imob Atualizada",
    "description": "Descrição atualizada da imobiliária",
    "email": "novo@imobexemplo.com",
    "phone": "11977777777",
    "website": "https://www.imobatualizada.com",
    "creci": "J-99999",
    "state_registration": "555555555",
    "active": true
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." \
  -d '{
    "query": "mutation UpdateRealEstate($id: ID!, $input: UpdateRealEstateInput!) { updateRealEstate(id: $id, input: $input) { id organization_id creci state_registration created_at updated_at organization { name fantasy_name cnpj description email phone website active } } }",
    "variables": { "id": "1", "input": { "name": "Imobiliária Atualizada", "fantasy_name": "Imob Atualizada", "description": "Descrição atualizada da imobiliária", "email": "novo@imobexemplo.com", "phone": "11977777777", "website": "https://www.imobatualizada.com", "creci": "J-99999", "state_registration": "555555555", "active": true } }
  }'
```

**Technical Implementation:**

The `updateRealEstate` mutation uses the `@spread` directive to flatten the input object and is implemented in the `UpdateRealEstateResolver` class, which:

1. Validates the user has permission to update real estate entities
2. Enforces multi-tenant access control rules
3. Updates both the Organization and RealEstate records in a single transaction
4. Updates the address if provided
5. Returns the updated real estate entity

### Validation Errors

- **422 Validation Failed**: Returned when input validation fails

Example:
```json
{
  "errors": [
    {
      "message": "Validation failed for the field [createRealEstate].",
      "extensions": {
        "validation": {
          "input.cnpj": ["The CNPJ field is required."],
          "input.email": ["The email field must be a valid email address."]
        }
      }
    }
  ]
}
```

### Not Found Errors

- **404 Not Found**: Returned when the requested real estate agency doesn't exist

Example:
```json
{
  "errors": [
    {
      "message": "No query results for model [Modules\\RealEstate\\Models\\RealEstate] 999",
      "extensions": {
        "category": "not-found"
      }
    }
  ]
}
```

## Examples

### Complete Real Estate Creation

This example demonstrates creating a real estate agency with an address using variables:

```graphql
mutation CreateRealEstateComplete($input: CreateRealEstateInput!) {
  createRealEstate(input: $input) {
    id
    organization_id
    creci
    state_registration
    created_at
    organization {
      name
      fantasy_name
      cnpj
      email
      active
      addresses {
        street
        number
        city
        state
      }
    }
  }
}
```

**Variables:**
```json
{
  "input": {
    "name": "Imobiliária Prime",
    "fantasy_name": "Prime Imóveis",
    "cnpj": "12345678901234",
    "description": "Imobiliária especializada em imóveis de luxo",
    "email": "contato@primeimob.com",
    "phone": "11999999999",
    "website": "https://www.primeimob.com",
    "creci": "J-12345",
    "state_registration": "123456789",
    "active": true,
    "address": {
      "street": "Avenida Paulista",
      "number": "1500",
      "complement": "Sala 150",
      "neighborhood": "Bela Vista",
      "city": "São Paulo",
      "state": "SP",
      "zip_code": "01310200",
      "country": "BR"
    }
  }
}
```

### Query Real Estate with Full Details

```graphql
query GetRealEstateDetails($id: ID!) {
  realEstate(id: $id) {
    id
    organization_id
    creci
    state_registration
    created_at
    updated_at
    
    # Organization information
    organization {
      name
      fantasy_name
      cnpj
      description
      email
      phone
      website
      active
      
      # Address information
      addresses {
        id
        type
        street
        number
        complement
        neighborhood
        city
        state
        zip_code
        country
        active
      }
      
      # Memberships (if available through Organization module)
      memberships {
        id
        role
        position
        is_active
        user {
          id
          name
          email
        }
      }
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
