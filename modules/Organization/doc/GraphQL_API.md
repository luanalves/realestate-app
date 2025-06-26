# Organization Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL queries and mutations available in the Organization module.

> **UPDATE:**
> The Organization module now directly provides a mutation for creating generic organizations via the `createOrganization` mutation.
> - Use the `createOrganization` mutation to create generic organizations
> - For specialized organization types (like real estate organizations), use the appropriate module's mutations
>   (e.g., `createRealEstate` from the RealEstate module)

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

## Organization Creation

The Organization module now provides direct mutations for creating and updating organizations.

### Creating an Organization

To create a generic organization, use the `createOrganization` mutation. For specialized organization types (like real estate organizations), you may prefer to use the specific module's mutations.

### Creating a Generic Organization

Use this mutation to create a generic organization:

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
    organization_type
    created_at
    updated_at
  }
}
```

### Alternative: Use the RealEstate Module

For real estate specific organizations, you can also use the RealEstate module's mutation:

```graphql
mutation CreateRealEstate($input: CreateRealEstateInput!) {
  createRealEstate(input: $input) {
    id
    name
    fantasyName
    cnpj
    description
    email
    phone
    website
    active
    createdAt
    updatedAt
  }
}
```

**Example Variables:**
```json
{
  "input": {
    "name": "Example Organization",
    "fantasyName": "Example Co.",
    "cnpj": "12345678901234",
    "description": "A sample organization",
    "email": "contact@example.com",
    "phone": "+1-555-0123",
    "website": "https://example.com",
    "active": true,
    "address": {
      "street": "Main Avenue",
      "number": "1000",
      "complement": "Suite 500",
      "neighborhood": "Downtown",
      "city": "New York",
      "state": "NY",
      "zipCode": "10001",
      "country": "US",
      "type": "headquarters"
    }
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateRealEstate($input: CreateRealEstateInput!) { createRealEstate(input: $input) { id name fantasyName cnpj description email phone website active createdAt updatedAt } }",
    "variables": {
      "input": {
        "name": "Example Organization",
        "fantasyName": "Example Co.",
        "cnpj": "12345678901234",
        "description": "A sample organization",
        "email": "contact@example.com",
        "phone": "+1-555-0123",
        "website": "https://example.com",
        "active": true,
        "address": {
          "street": "Main Avenue",
          "number": "1000",
          "complement": "Suite 500",
          "neighborhood": "Downtown",
          "city": "New York",
          "state": "NY",
          "zipCode": "10001",
          "country": "US",
          "type": "headquarters"
        }
      }
    }
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
      position
      isActive
      joinedAt
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

Retrieve a list of all organizations with pagination.

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
      organization_type
      created_at
      updated_at
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
    "query": "query GetOrganizations($first: Int!, $page: Int) { organizations(first: $first, page: $page) { data { id name fantasy_name cnpj description email phone website active organization_type created_at updated_at } paginatorInfo { count currentPage firstItem hasMorePages lastItem lastPage perPage total } } }",
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
      organization_type
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
    "query": "query GetOrganizationAddress($id: ID!) { organizationAddressById(id: $id) { id organization_id street number complement neighborhood city state zip_code country type active created_at updated_at organization { id name organization_type } } }",
    "variables": {
      "id": "1"
    }
  }'
```

### 4. Get Addresses by Organization ID

Retrieve all addresses for a specific organization. The `organizationType` argument is now optional and handled automatically for Organization addresses.

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
    organization_type
    created_at
    updated_at
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
    "active": true,
    "organization_type": "generic",
    "address": {
      "type": "headquarters",
      "street": "Main Street",
      "number": "123",
      "complement": "Floor 4",
      "neighborhood": "Downtown",
      "city": "São Paulo",
      "state": "SP",
      "zipCode": "01234567",
      "country": "BR"
    }
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { id name fantasy_name cnpj description email phone website active organization_type created_at updated_at } }",
    "variables": {
      "input": {
        "name": "New Organization",
        "fantasy_name": "Fancy Org Name",
        "cnpj": "12345678901234",
        "description": "This is a new generic organization",
        "email": "contact@neworg.com",
        "phone": "+55 11 99999-9999",
        "website": "https://neworg.com",
        "active": true,
        "organization_type": "generic",
        "address": {
          "type": "headquarters",
          "street": "Main Street",
          "number": "123",
          "complement": "Floor 4",
          "neighborhood": "Downtown",
          "city": "São Paulo",
          "state": "SP",
          "zipCode": "01234567",
          "country": "BR"
        }
      }
    }
  }'
```

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
    organization_type
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
    "query": "mutation UpdateOrganization($id: ID!, $input: UpdateOrganizationInput!) { updateOrganization(id: $id, input: $input) { id name fantasy_name description email phone website active organization_type created_at updated_at } }",
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
    organization_type
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
    "query": "mutation DeleteOrganization($id: ID!) { deleteOrganization(id: $id) { id name fantasy_name cnpj email organization_type } }",
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
    organization_type
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
        "addressType": "HEADQUARTERS"
      }
    }
  }'
```

3. **Add Team Member:**
```bash
curl -X POST http://realestate.localhost/graphql \
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
curl -X POST http://realestate.localhost/graphql \
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
