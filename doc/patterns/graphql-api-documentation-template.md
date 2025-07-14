# GraphQL API Documentation Template

## Overview

This template provides a standardized format for documenting GraphQL APIs in modules within the Real Estate application. It ensures consistency across all module documentation and follows best practices for developer experience.

## Purpose

- **Standardize** GraphQL API documentation across all modules
- **Improve** developer experience with consistent examples
- **Ensure** all necessary information is documented
- **Facilitate** easy copy-paste examples for testing

## Template Structure

### File Location
```
modules/{ModuleName}/doc/GraphQL_API.md
```

### Template Content

```markdown
# {ModuleName} Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL queries and mutations available in the {ModuleName} module.

## Table of Contents

1. [Introduction](#introduction)
2. [Authentication](#authentication)
3. [{ModuleName} Queries](#modulename-queries)
   - [Get All {Entities}](#1-get-all-entities)
   - [Get {Entity} by ID](#2-get-entity-by-id)
4. [{ModuleName} Mutations](#modulename-mutations)
   - [Create {Entity}](#1-create-entity)
   - [Update {Entity}](#2-update-entity)
   - [Delete {Entity}](#3-delete-entity)
5. [Error Handling](#error-handling)
6. [Examples](#examples)

## Introduction

Brief description of what this module provides and its main purpose.

## Authentication

**Important**: Describe authentication requirements for this module.

### Required Permissions

List the permissions required for different operations:

- **Read operations**: Permission requirements
- **Write operations**: Permission requirements and roles

### Multi-Tenant Access Control (if applicable)

Describe any multi-tenant access control rules.

## Integration with Organization Module (Optional)

> **Note**: This section is only applicable if your module extends the Organization module through the extension system.

If your module integrates with the Organization module's extension system, document how to access module-specific data through organization queries.

### Getting Organization Data with Module Extensions

**Query:**
```graphql
query GetOrganizationWithModuleData($id: ID!) {
  organization(id: $id) {
    id
    name
    description
    extensionData  # Contains module-specific data
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
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "query": "query GetOrganizationWithModuleData($id: ID!) { organization(id: $id) { id name description extensionData } }",
    "variables": {
      "id": "1"
    }
  }' \
  "http://realestate.localhost/graphql"
```

**Response:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Organization Name",
      "description": "Organization Description",
      "extensionData": "{\"yourModule\":{\"id\":1,\"specific_field\":\"value\"}}"
    }
  }
}
```

### When to Use Each Approach

**Use Organization Extension System when:**
- You need complete organization + module-specific data
- You want to access organization addresses
- You're building user interfaces that show organization information
- You need to support multiple organization types

**Use Direct Module Queries when:**
- You only need module-specific data
- You're building reports or exports focused on module data
- You need specialized filtering or sorting
- You're performing bulk operations on module entities

## {ModuleName} Queries

### 1. Get All {Entities}

Brief description of what this query does.

**Authentication Required:** Yes/No

**Query:**
```graphql
query Get{Entities}($first: Int, $page: Int) {
  {entities}(
    first: $first, 
    page: $page,
    orderBy: [{ column: ID, order: ASC }]
  ) {
    data {
      id
      # Add relevant fields
      created_at
      updated_at
    }
    paginatorInfo {
      currentPage
      lastPage
      total
      count
      hasMorePages
      firstItem
      lastItem
      perPage
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
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." \
  -d '{
    "query": "query Get{Entities}($first: Int, $page: Int) { {entities}(first: $first, page: $page, orderBy: [{ column: ID, order: ASC }]) { data { id created_at updated_at } paginatorInfo { currentPage lastPage total count hasMorePages firstItem lastItem perPage } } }",
    "variables": { "first": 10, "page": 1 }
  }'
```

**Response:**
```json
{
  "data": {
    "{entities}": {
      "data": [
        {
          "id": "1",
          "created_at": "2025-07-05T00:00:00Z",
          "updated_at": "2025-07-05T00:00:00Z"
        }
      ],
      "paginatorInfo": {
        "currentPage": 1,
        "lastPage": 1,
        "total": 1,
        "count": 1,
        "hasMorePages": false,
        "firstItem": 1,
        "lastItem": 1,
        "perPage": 10
      }
    }
  }
}
```

### 2. Get {Entity} by ID

Brief description of what this query does.

**Authentication Required:** Yes/No

**Query:**
```graphql
query Get{Entity}($id: ID!) {
  {entity}(id: $id) {
    id
    # Add relevant fields
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
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." \
  -d '{
    "query": "query Get{Entity}($id: ID!) { {entity}(id: $id) { id created_at updated_at } }",
    "variables": { "id": "1" }
  }'
```

**Technical Implementation:** (Optional)

Brief description of the resolver implementation and any special considerations.

## {ModuleName} Mutations

### 1. Create {Entity}

Brief description of what this mutation does.

**Authentication Required:** Yes (specify roles)

**Mutation:**
```graphql
mutation Create{Entity}($input: Create{Entity}Input!) {
  create{Entity}(input: $input) {
    id
    # Add relevant fields
    created_at
    updated_at
  }
}
```

**Variables:**
```json
{
  "input": {
    "name": "Example Name",
    "description": "Example Description"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." \
  -d '{
    "query": "mutation Create{Entity}($input: Create{Entity}Input!) { create{Entity}(input: $input) { id created_at updated_at } }",
    "variables": { "input": { "name": "Example Name", "description": "Example Description" } }
  }'
```

**Technical Implementation:** (Optional)

Brief description of the resolver implementation.

### 2. Update {Entity}

Brief description of what this mutation does.

**Authentication Required:** Yes (specify roles)

**Mutation:**
```graphql
mutation Update{Entity}($id: ID!, $input: Update{Entity}Input!) {
  update{Entity}(id: $id, input: $input) {
    id
    # Add relevant fields
    updated_at
  }
}
```

**Variables:**
```json
{
  "id": "1",
  "input": {
    "name": "Updated Name",
    "description": "Updated Description"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." \
  -d '{
    "query": "mutation Update{Entity}($id: ID!, $input: Update{Entity}Input!) { update{Entity}(id: $id, input: $input) { id updated_at } }",
    "variables": { "id": "1", "input": { "name": "Updated Name", "description": "Updated Description" } }
  }'
```

### 3. Delete {Entity}

Brief description of what this mutation does.

**Authentication Required:** Yes (specify roles)

**Mutation:**
```graphql
mutation Delete{Entity}($id: ID!) {
  delete{Entity}(id: $id) {
    id
    # Add relevant fields for confirmation
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
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." \
  -d '{
    "query": "mutation Delete{Entity}($id: ID!) { delete{Entity}(id: $id) { id } }",
    "variables": { "id": "1" }
  }'
```

## Error Handling

Standard error handling patterns used in this module:

### Authentication Errors

- **401 Unauthenticated**: Returned when the request lacks valid authentication credentials
- **403 Forbidden**: Returned when the authenticated user lacks permissions for an operation

Example:
```json
{
  "errors": [
    {
      "message": "You do not have permission to access this resource",
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
      "message": "Validation failed for the field [create{Entity}].",
      "extensions": {
        "validation": {
          "input.name": ["The name field is required."],
          "input.email": ["The email field must be a valid email address."]
        }
      }
    }
  ]
}
```

### Not Found Errors

- **404 Not Found**: Returned when the requested resource doesn't exist

Example:
```json
{
  "errors": [
    {
      "message": "No query results for model [Modules\\{ModuleName}\\Models\\{Entity}] {id}",
      "extensions": {
        "category": "not-found"
      }
    }
  ]
}
```

## Examples

### Complete {Entity} Creation Flow

Example demonstrating a complete workflow:

```graphql
mutation Create{Entity}Complete($input: Create{Entity}Input!) {
  create{Entity}(input: $input) {
    id
    # Add relevant fields
    created_at
  }
}
```

**Variables:**
```json
{
  "input": {
    "name": "Complete Example",
    "description": "This is a complete example"
  }
}
```

### Query {Entity} with Full Details

Example showing how to query with all related data:

```graphql
query Get{Entity}Details($id: ID!) {
  {entity}(id: $id) {
    id
    # Add all relevant fields and relationships
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
```

## Usage Instructions

### 1. Copy the Template

Copy the template content above and create a new file at:
```
modules/{ModuleName}/doc/GraphQL_API.md
```

### 2. Replace Placeholders

Replace the following placeholders with actual values:

- `{ModuleName}` → Actual module name (e.g., "RealEstate", "UserManagement")
- `{Entity}` → Singular entity name (e.g., "RealEstate", "User")
- `{Entities}` → Plural entity name (e.g., "RealEstates", "Users")
- `{entities}` → Plural entity name in camelCase (e.g., "realEstates", "users")
- `{entity}` → Singular entity name in camelCase (e.g., "realEstate", "user")

### 3. Customize Content

- **Add module-specific fields** to GraphQL queries and mutations
- **Include relationships** that are relevant to your entities
- **Add specific authentication requirements** for your module
- **Include any special technical implementation notes**
- **Add module-specific error cases** if applicable

### 4. Variable Standards

Always use **literal values** in the Variables blocks:

**✅ Correct:**
```json
{
  "first": 10,
  "page": 1,
  "id": "1"
}
```

**❌ Incorrect:**
```json
{
  "first": "{{PAGINATION_FIRST}}",
  "page": "{{PAGINATION_PAGE}}",
  "id": "{{ENTITY_ID}}"
}
```

### 5. cURL Examples

Use consistent values in cURL examples:

- **Base URL**: `http://realestate.localhost/graphql`
- **Token**: `eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...`
- **Content-Type**: `application/json`

### 6. Best Practices

1. **Always include Variables blocks** for queries and mutations that use variables
2. **Use realistic example values** that developers can understand
3. **Include technical implementation notes** for complex operations
4. **Document authentication requirements** clearly
5. **Provide complete cURL examples** that work out of the box
6. **Include error examples** for common scenarios
7. **Add response examples** for successful operations

## Benefits

- **Consistency**: All modules follow the same documentation pattern
- **Developer Experience**: Easy to understand and use examples
- **Testing**: Copy-paste ready examples for immediate testing
- **Maintenance**: Standardized format makes updates easier
- **Onboarding**: New developers can quickly understand GraphQL APIs

## Related Patterns

- [GraphQL Module Integration Solution](./graphql-module-integration-solution.md)
- [GraphQL Pagination Pattern](./graphql-pagination-pattern.md)
- [Authorization Service Pattern](./authorization-service-pattern.md)

## Example Implementation

See the RealEstate module documentation for a complete example:
```
modules/RealEstate/doc/GraphQL_API.md
```

This serves as a reference implementation of this template pattern.
