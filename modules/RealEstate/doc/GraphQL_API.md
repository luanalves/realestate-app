# RealEstate Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL queries and mutations available in the RealEstate module.

## Table of Contents

1. [Introduction](#introduction)
2. [Authentication](#authentication)
3. [RealEstate Queries](#realestate-queries)
   - [Get All Real Estates](#1-get-all-real-estates)
   - [Get Real Estate by ID](#2-get-real-estate-by-id)
4. [RealEstate Mutations](#realestate-mutations)
   - [Create Real Estate](#1-create-real-estate)
   - [Update Real Estate](#2-update-real-estate)
   - [Delete Real Estate](#3-delete-real-estate)
5. [Error Handling](#error-handling)
6. [Examples](#examples)

## Introduction

The RealEstate module provides GraphQL operations for managing real estate agencies within the system. It allows authorized users to create, read, update, and delete real estate entities.

## Authentication

**Important**: All operations in the RealEstate module require authentication. The system uses Laravel Passport for OAuth token-based authentication.

### Required Permissions

Operations within the RealEstate module require specific permissions:

- **Read operations**: Any authenticated user can read real estate data
- **Write operations (create, update, delete)**: Restricted to users with the following roles:
  - `super_admin`
  - `real_estate_admin`

### Multi-Tenant Access Control

The RealEstate module implements multi-tenant access control:

- `super_admin` users can access all real estate entities
- Other users can only access real estate entities within their assigned tenant

## RealEstate Queries

### 1. Get All Real Estates

Retrieve a paginated list of all real estate agencies.

**Authentication Required:** Yes

**Query:**
```graphql
query {
  realEstates(
    first: 10, 
    page: 1,
    orderBy: { field: "name", order: ASC }
  ) {
    data {
      id
      name
      fantasyName
      cnpj
      description
      email
      phone
      website
      creci
      stateRegistration
      active
      created_at
      updated_at
    }
    paginatorInfo {
      currentPage
      lastPage
      total
      count
      hasMorePages
    }
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "query { realEstates(first: 10, page: 1) { data { id name fantasyName cnpj description email phone website creci stateRegistration active created_at updated_at } paginatorInfo { currentPage lastPage total count hasMorePages } } }"
  }'
```

**Response:**
```json
{
  "data": {
    "realEstates": {
      "data": [
        {
          "id": "1",
          "name": "Imobiliária Exemplo",
          "fantasyName": "Imob Exemplo",
          "cnpj": "12345678901234",
          "description": "Uma imobiliária especializada em imóveis de alto padrão",
          "email": "contato@imobexemplo.com",
          "phone": "(11) 99999-9999",
          "website": "https://www.imobexemplo.com",
          "creci": "J-12345",
          "stateRegistration": "123456789",
          "active": true,
          "created_at": "2025-06-29T10:00:00Z",
          "updated_at": "2025-06-29T15:30:00Z"
        }
        // More real estates...
      ],
      "paginatorInfo": {
        "currentPage": 1,
        "lastPage": 3,
        "total": 25,
        "count": 10,
        "hasMorePages": true
      }
    }
  }
}
```

### 2. Get Real Estate by ID

Retrieve a specific real estate agency by its ID.

**Authentication Required:** Yes

**Query:**
```graphql
query($id: ID!) {
  realEstate(id: $id) {
    id
    name
    fantasyName
    cnpj
    description
    email
    phone
    website
    creci
    stateRegistration
    active
    members {
      id
      name
      email
    }
    mainAddress {
      id
      street
      number
      complement
      neighborhood
      city
      state
      zip_code
      country
    }
    created_at
    updated_at
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
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "query($id: ID!) { realEstate(id: $id) { id name fantasyName cnpj description email phone website creci stateRegistration active members { id name email } mainAddress { id street number complement neighborhood city state zip_code country } created_at updated_at } }",
    "variables": { "id": "1" }
  }'
```

**Technical Implementation:**

The `realEstate(id: ID!)` query is implemented in the `RealEstateResolver` class, which:

1. Validates the user has permission to access the real estate data
2. Enforces multi-tenant access control rules
3. Retrieves the real estate entity with its related organization and addresses
4. Returns the real estate or throws a `ModelNotFoundException` if not found

## RealEstate Mutations

### 1. Create Real Estate

Create a new real estate agency.

**Authentication Required:** Yes (super_admin or real_estate_admin roles only)

**Mutation:**
```graphql
mutation($input: CreateRealEstateInput!) {
  createRealEstate(input: $input) {
    id
    name
    fantasyName
    cnpj
    description
    email
    phone
    website
    creci
    stateRegistration
    active
    created_at
    updated_at
  }
}
```

**Input:**
```json
{
  "input": {
    "name": "Nova Imobiliária",
    "fantasyName": "Nova Imob",
    "cnpj": "98765432109876",
    "description": "Descrição da nova imobiliária",
    "email": "contato@novaimob.com",
    "phone": "(11) 98888-8888",
    "website": "https://www.novaimob.com",
    "creci": "J-54321",
    "stateRegistration": "987654321",
    "active": true,
    "address": {
      "street": "Avenida Principal",
      "number": "1000",
      "complement": "Sala 101",
      "neighborhood": "Centro",
      "city": "São Paulo",
      "state": "SP",
      "zip_code": "01000-000",
      "country": "Brasil"
    }
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "query": "mutation($input: CreateRealEstateInput!) { createRealEstate(input: $input) { id name fantasyName cnpj description email phone website creci stateRegistration active created_at updated_at } }",
    "variables": { "input": { "name": "Nova Imobiliária", "fantasyName": "Nova Imob", "cnpj": "98765432109876", "description": "Descrição da nova imobiliária", "email": "contato@novaimob.com", "phone": "(11) 98888-8888", "website": "https://www.novaimob.com", "creci": "J-54321", "stateRegistration": "987654321", "active": true, "address": { "street": "Avenida Principal", "number": "1000", "complement": "Sala 101", "neighborhood": "Centro", "city": "São Paulo", "state": "SP", "zip_code": "01000-000", "country": "Brasil" } } }
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
mutation($id: ID!, $input: UpdateRealEstateInput!) {
  updateRealEstate(id: $id, input: $input) {
    id
    name
    fantasyName
    cnpj
    description
    email
    phone
    website
    creci
    stateRegistration
    active
    created_at
    updated_at
  }
}
```

**Input:**
```json
{
  "id": "1",
  "input": {
    "name": "Imobiliária Atualizada",
    "fantasyName": "Imob Atualizada",
    "description": "Descrição atualizada da imobiliária",
    "email": "novo@imobexemplo.com",
    "phone": "(11) 97777-7777",
    "website": "https://www.imobatualizada.com",
    "creci": "J-99999",
    "stateRegistration": "555555555",
    "active": true
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "query": "mutation($id: ID!, $input: UpdateRealEstateInput!) { updateRealEstate(id: $id, input: $input) { id name fantasyName cnpj description email phone website creci stateRegistration active created_at updated_at } }",
    "variables": { "id": "1", "input": { "name": "Imobiliária Atualizada", "fantasyName": "Imob Atualizada", "description": "Descrição atualizada da imobiliária", "email": "novo@imobexemplo.com", "phone": "(11) 97777-7777", "website": "https://www.imobatualizada.com", "creci": "J-99999", "stateRegistration": "555555555", "active": true } }
  }'
```

**Technical Implementation:**

The `updateRealEstate` mutation uses the `@spread` directive to flatten the input object and is implemented in the `UpdateRealEstateResolver` class, which:

1. Validates the user has permission to update real estate entities
2. Enforces multi-tenant access control rules
3. Updates both the Organization and RealEstate records in a single transaction
4. Updates the address if provided
5. Returns the updated real estate entity

### 3. Delete Real Estate

Delete an existing real estate agency.

**Authentication Required:** Yes (super_admin or real_estate_admin roles only)

**Mutation:**
```graphql
mutation($id: ID!) {
  deleteRealEstate(id: $id) {
    id
    name
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
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "query": "mutation($id: ID!) { deleteRealEstate(id: $id) { id name } }",
    "variables": { "id": "1" }
  }'
```

**Technical Implementation:**

The `deleteRealEstate` mutation is implemented in the `DeleteRealEstateResolver` class, which:

1. Validates the user has permission to delete real estate entities
2. Enforces multi-tenant access control rules
3. Deletes the RealEstate and associated Organization records in a single transaction
4. Deletes associated addresses through cascading
5. Returns the deleted real estate entity information

## Error Handling

The RealEstate module follows these error handling patterns:

### Authentication Errors

- **401 Unauthenticated**: Returned when the request lacks valid authentication credentials
- **403 Forbidden**: Returned when the authenticated user lacks permissions for an operation

Example:
```json
{
  "errors": [
    {
      "message": "You do not have permission to modify real estate agencies",
      "extensions": {
        "category": "authorization"
      }
    }
  ]
}
```

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

This example demonstrates creating a real estate agency with an address:

```graphql
mutation {
  createRealEstate(input: {
    name: "Imobiliária Prime",
    fantasyName: "Prime Imóveis",
    cnpj: "12345678901234",
    description: "Imobiliária especializada em imóveis de luxo",
    email: "contato@primeimob.com",
    phone: "(11) 99999-9999",
    website: "https://www.primeimob.com",
    creci: "J-12345",
    stateRegistration: "123456789",
    active: true,
    address: {
      street: "Avenida Paulista",
      number: "1500",
      complement: "Sala 150",
      neighborhood: "Bela Vista",
      city: "São Paulo",
      state: "SP",
      zip_code: "01310-200",
      country: "Brasil",
      type: "headquarters"
    }
  }) {
    id
    name
    fantasyName
    cnpj
    email
    creci
    active
    mainAddress {
      street
      number
      city
      state
    }
    created_at
  }
}
```

### Query Real Estate with Full Details

```graphql
query {
  realEstate(id: 1) {
    id
    name
    fantasyName
    cnpj
    description
    email
    phone
    website
    creci
    stateRegistration
    active
    
    # Member information
    members {
      id
      name
      email
      role {
        name
      }
    }
    
    # Address information
    mainAddress {
      street
      number
      complement
      neighborhood
      city
      state
      zip_code
      country
    }
    
    # All addresses
    addresses {
      id
      type
      street
      number
      city
      state
    }
    
    created_at
    updated_at
  }
}
```
