# Organization Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL queries and mutations available in the Organization module.

## Table of Contents

1. [Authentication](#authentication)
2. [Organization Queries](#organization-queries)
3. [Organization Mutations](#organization-mutations)
4. [Organization Member Operations](#organization-member-operations)
5. [Organization Address Operations](#organization-address-operations)
6. [Error Handling](#error-handling)
7. [Examples](#examples)

## Authentication

All GraphQL operations in the Organization module require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer your_access_token_here
```

To obtain an access token, make a POST request to `/oauth/token`:

```bash
curl -X POST http://localhost:8000/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "password",
    "client_id": "1",
    "client_secret": "your_client_secret",
    "username": "your_email@example.com",
    "password": "your_password"
  }'
```

## Organization Queries

### 1. Get Organization by ID

Retrieve a specific organization by its ID.

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
    organization_type
    created_at
    updated_at
    members {
      id
      user {
        id
        name
        email
      }
      role
      joined_at
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

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganization($id: ID!) { organization(id: $id) { id name fantasy_name cnpj description email phone website active organization_type created_at updated_at addresses { id street number complement neighborhood city state zip_code country type active } } }",
    "variables": {
      "id": "1"
    }
  }'
```

### 2. Get All Organizations

Retrieve a list of all organizations with optional filtering.

**Query:**
```graphql
query GetOrganizations($first: Int, $page: Int) {
  organizations(first: $first, page: $page) {
    data {
      id
      name
      type
      description
      email
      phone
      website
      isActive
      foundedAt
      createdAt
      updatedAt
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
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganizations($first: Int, $page: Int) { organizations(first: $first, page: $page) { data { id name type description email phone website isActive foundedAt createdAt updatedAt } paginatorInfo { count currentPage firstItem hasMorePages lastItem lastPage perPage total } } }",
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
  organizationAddress(id: $id) {
    id
    organizationId
    street
    number
    complement
    district
    city
    state
    zipCode
    country
    isMainAddress
    addressType
    createdAt
    updatedAt
    organization {
      id
      name
      type
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

### 4. Get Addresses by Organization ID

Retrieve all addresses for a specific organization.

**Query:**
```graphql
query GetAddressesByOrganization($organizationId: ID!) {
  addressesByOrganizationId(organizationId: $organizationId) {
    id
    organizationId
    street
    number
    complement
    district
    city
    state
    zipCode
    country
    isMainAddress
    addressType
    createdAt
    updatedAt
  }
}
```

**Variables:**
```json
{
  "organizationId": "1"
}
```

## Organization Mutations

### 1. Create Organization

Create a new organization.

**Mutation:**
```graphql
mutation CreateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    id
    name
    type
    description
    email
    phone
    website
    isActive
    foundedAt
    createdAt
    updatedAt
  }
}
```

**Variables:**
```json
{
  "input": {
    "name": "Tech Solutions Inc",
    "type": "TECHNOLOGY",
    "description": "A leading technology solutions provider",
    "email": "contact@techsolutions.com",
    "phone": "+1-555-0123",
    "website": "https://techsolutions.com",
    "isActive": true,
    "foundedAt": "2020-01-15"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { id name type description email phone website isActive foundedAt createdAt updatedAt } }",
    "variables": {
      "input": {
        "name": "Tech Solutions Inc",
        "type": "TECHNOLOGY",
        "description": "A leading technology solutions provider",
        "email": "contact@techsolutions.com",
        "phone": "+1-555-0123",
        "website": "https://techsolutions.com",
        "isActive": true,
        "foundedAt": "2020-01-15"
      }
    }
  }'
```

### 2. Update Organization

Update an existing organization.

**Mutation:**
```graphql
mutation UpdateOrganization($id: ID!, $input: UpdateOrganizationInput!) {
  updateOrganization(id: $id, input: $input) {
    id
    name
    type
    description
    email
    phone
    website
    isActive
    foundedAt
    createdAt
    updatedAt
  }
}
```

**Variables:**
```json
{
  "id": "1",
  "input": {
    "name": "Tech Solutions International",
    "description": "A global technology solutions provider",
    "phone": "+1-555-0124",
    "website": "https://techsolutions-intl.com"
  }
}
```

### 3. Delete Organization

Delete an organization by ID.

**Mutation:**
```graphql
mutation DeleteOrganization($id: ID!) {
  deleteOrganization(id: $id) {
    id
    name
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

## Organization Member Operations

### 1. Add Organization Member

Add a user as a member to an organization.

**Mutation:**
```graphql
mutation AddOrganizationMember($input: AddOrganizationMemberInput!) {
  addOrganizationMember(input: $input) {
    id
    organizationId
    userId
    role
    joinedAt
    user {
      id
      name
      email
    }
    organization {
      id
      name
      type
    }
  }
}
```

**Variables:**
```json
{
  "input": {
    "organizationId": "1",
    "userId": "2",
    "role": "ADMIN"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation AddOrganizationMember($input: AddOrganizationMemberInput!) { addOrganizationMember(input: $input) { id organizationId userId role joinedAt user { id name email } organization { id name type } } }",
    "variables": {
      "input": {
        "organizationId": "1",
        "userId": "2",
        "role": "ADMIN"
      }
    }
  }'
```

### 2. Update Organization Member

Update a member's role in an organization.

**Mutation:**
```graphql
mutation UpdateOrganizationMember($input: UpdateOrganizationMemberInput!) {
  updateOrganizationMember(input: $input) {
    id
    organizationId
    userId
    role
    joinedAt
    user {
      id
      name
      email
    }
    organization {
      id
      name
      type
    }
  }
}
```

**Variables:**
```json
{
  "input": {
    "organizationId": "1",
    "userId": "2",
    "role": "MANAGER"
  }
}
```

### 3. Remove Organization Member

Remove a member from an organization.

**Mutation:**
```graphql
mutation RemoveOrganizationMember($input: RemoveOrganizationMemberInput!) {
  removeOrganizationMember(input: $input) {
    id
    organizationId
    userId
    role
    user {
      id
      name
      email
    }
    organization {
      id
      name
      type
    }
  }
}
```

**Variables:**
```json
{
  "input": {
    "organizationId": "1",
    "userId": "2"
  }
}
```

## Organization Address Operations

### 1. Create Organization Address

Add a new address to an organization.

**Mutation:**
```graphql
mutation CreateOrganizationAddress($input: CreateOrganizationAddressInput!) {
  createOrganizationAddress(input: $input) {
    id
    organizationId
    street
    number
    complement
    district
    city
    state
    zipCode
    country
    isMainAddress
    addressType
    createdAt
    updatedAt
    organization {
      id
      name
    }
  }
}
```

**Variables:**
```json
{
  "input": {
    "organizationId": "1",
    "street": "Main Street",
    "number": "123",
    "complement": "Suite 456",
    "district": "Downtown",
    "city": "San Francisco",
    "state": "CA",
    "zipCode": "94105",
    "country": "USA",
    "isMainAddress": true,
    "addressType": "HEADQUARTERS"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganizationAddress($input: CreateOrganizationAddressInput!) { createOrganizationAddress(input: $input) { id organizationId street number complement district city state zipCode country isMainAddress addressType createdAt updatedAt organization { id name } } }",
    "variables": {
      "input": {
        "organizationId": "1",
        "street": "Main Street",
        "number": "123",
        "complement": "Suite 456",
        "district": "Downtown",
        "city": "San Francisco",
        "state": "CA",
        "zipCode": "94105",
        "country": "USA",
        "isMainAddress": true,
        "addressType": "HEADQUARTERS"
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
    organizationId
    street
    number
    complement
    district
    city
    state
    zipCode
    country
    isMainAddress
    addressType
    createdAt
    updatedAt
    organization {
      id
      name
    }
  }
}
```

**Variables:**
```json
{
  "id": "1",
  "input": {
    "street": "New Main Street",
    "number": "456",
    "complement": "Floor 2",
    "district": "Business District",
    "isMainAddress": false,
    "addressType": "BRANCH"
  }
}
```

### 3. Delete Organization Address

Delete an organization address.

**Mutation:**
```graphql
mutation DeleteOrganizationAddress($id: ID!) {
  deleteOrganizationAddress(id: $id) {
    id
    organizationId
    street
    number
    city
    state
    country
    addressType
  }
}
```

**Variables:**
```json
{
  "id": "1"
}
```

## Error Handling

### Common Error Responses

#### Authentication Error
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

#### Validation Error
```json
{
  "errors": [
    {
      "message": "Validation failed for the field [createOrganization].",
      "extensions": {
        "validation": {
          "input.name": [
            "The name field is required."
          ],
          "input.email": [
            "The email must be a valid email address."
          ]
        }
      }
    }
  ]
}
```

#### Not Found Error
```json
{
  "errors": [
    {
      "message": "Organization not found with ID: 999",
      "extensions": {
        "category": "graphql"
      }
    }
  ]
}
```

#### Authorization Error
```json
{
  "errors": [
    {
      "message": "You are not authorized to perform this action.",
      "extensions": {
        "category": "authorization"
      }
    }
  ]
}
```

## Examples

### Complete Organization Creation Workflow

1. **Create Organization:**
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { id name type email } }",
    "variables": {
      "input": {
        "name": "Innovation Labs",
        "type": "TECHNOLOGY",
        "email": "contact@innovationlabs.com",
        "phone": "+1-555-0100"
      }
    }
  }'
```

2. **Add Main Address:**
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganizationAddress($input: CreateOrganizationAddressInput!) { createOrganizationAddress(input: $input) { id street number city state country } }",
    "variables": {
      "input": {
        "organizationId": "1",
        "street": "Tech Avenue",
        "number": "789",
        "city": "Silicon Valley",
        "state": "CA",
        "zipCode": "94000",
        "country": "USA",
        "isMainAddress": true,
        "addressType": "HEADQUARTERS"
      }
    }
  }'
```

3. **Add Team Member:**
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation AddOrganizationMember($input: AddOrganizationMemberInput!) { addOrganizationMember(input: $input) { id role user { name email } } }",
    "variables": {
      "input": {
        "organizationId": "1",
        "userId": "2",
        "role": "ADMIN"
      }
    }
  }'
```

4. **Query Complete Organization:**
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "query GetOrganization($id: ID!) { organization(id: $id) { id name type email phone website members { user { name email } role } addresses { street number city state isMainAddress addressType } } }",
    "variables": {
      "id": "1"
    }
  }'
```

## Available Organization Types

The following organization types are available:

- `CORPORATION`
- `LLC`
- `PARTNERSHIP`
- `SOLE_PROPRIETORSHIP`
- `NON_PROFIT`
- `GOVERNMENT`
- `EDUCATIONAL`
- `HEALTHCARE`
- `TECHNOLOGY`
- `FINANCIAL`
- `REAL_ESTATE`
- `RETAIL`
- `MANUFACTURING`
- `CONSULTING`
- `OTHER`

## Available Member Roles

The following member roles are available:

- `OWNER`
- `ADMIN`
- `MANAGER`
- `MEMBER`
- `GUEST`

## Available Address Types

The following address types are available:

- `HEADQUARTERS`
- `BRANCH`
- `WAREHOUSE`
- `OFFICE`
- `RETAIL`
- `MANUFACTURING`
- `OTHER`

---

**Note:** Replace `your_access_token` with your actual OAuth access token in all examples. The GraphQL Playground is available at `/graphql-playground` for interactive testing.
