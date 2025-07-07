# RealEstate Module - GraphQL API Documentation

## Introduction

This document provides comprehensive documentation for the RealEstate module GraphQL API, which manages real estate agencies and their properties in the application. The RealEstate module is now fully integrated with the Organization module through a decoupled architecture based on the Observer Pattern.

> **ARCHITECTURE - IMPORTANT UPDATE:**
> The RealEstate module implements a **fully decoupled architecture** through Observer pattern integration:
> 
> - **✅ Creation via Organization**: Real estate agencies are created through Organization module with `extensionData`
> - **✅ Observer Pattern**: RealEstate data is automatically managed through Organization events
> - **✅ No Direct Dependencies**: RealEstate module doesn't depend on Organization module
> - **✅ Extension Data**: Real estate data is accessible through Organization queries via `extensionData`
>
> **Key Changes:**
> - Removed `createRealEstate` mutation - use Organization `createOrganization` with `extensionData`
> - Real estate data accessible via Organization queries with `extensionData` field
> - Automatic RealEstate record creation/update through Organization event listeners
> - Only `updateRealEstate` mutation remains for direct real estate updates

### Observer Pattern Integration

The RealEstate module uses the Observer Pattern to listen for Organization events:

1. When an Organization is created or updated with RealEstate extension data:
   - The RealEstateObserver receives the event
   - It extracts RealEstate data from the Organization's extensionData
   - It creates or updates the corresponding RealEstate record

2. Benefits of this architecture:
   - **Loose Coupling**: Modules operate independently
   - **Separation of Concerns**: Each module handles its specific domain
   - **Scalability**: New extensions can be added without modifying Organization module
   - **Maintainability**: Changes to one module don't require changes to others

## Table of Contents

1. [Introduction](#introduction)
   - [Observer Pattern Integration](#observer-pattern-integration)
2. [Architecture Overview](#architecture-overview)
   - [Component Diagram](#component-diagram)
   - [Data Flow](#data-flow)
   - [Schema Structure](#schema-structure)
3. [Integration with Organization Module](#integration-with-organization-module)
   - [Using extensionData](#using-extensiondata)
   - [Creating Real Estate Organizations](#creating-real-estate-organizations)
   - [Querying Real Estate Organizations](#querying-real-estate-organizations)
   - [Updating Real Estate Organizations](#updating-real-estate-organizations)
   - [Direct RealEstate Extension Updates](#direct-realestate-extension-updates)
   - [Filtering Organizations by RealEstate Extension Data](#filtering-organizations-by-realestate-extension-data)
4. [Executive Summary](#executive-summary)
   - [Quick Start](#quick-start)
   - [Key Features](#key-features)

5. [Error Handling](#error-handling)
   - [Error Types](#error-types)
   - [Validation Errors](#validation-errors)
   - [Authentication Errors](#authentication-errors)
   - [Authorization Errors](#authorization-errors)
   - [Not Found Errors](#not-found-errors)
   - [Internal Server Errors](#internal-server-errors)
   - [Client-Side Error Handling](#client-side-error-handling)
   - [GraphQL Error Codes](#graphql-error-codes)
   - [Logging and Monitoring](#logging-and-monitoring)
   - [Error Handling During Integration](#error-handling-during-integration)
6. [Testing and Integration](#testing-and-integration)
   - [Testing RealEstate GraphQL API](#testing-realestate-graphql-api)
   - [Running Tests](#running-tests)
   - [Integration Best Practices](#integration-best-practices)
   - [Migrating from Legacy API](#migrating-from-legacy-api)
8. [Troubleshooting](#troubleshooting)
   - [Common Issues and Solutions](#common-issues-and-solutions)
   - [Logging and Debugging](#logging-and-debugging)
   - [Support Resources](#support-resources)
9. [Conclusion and Best Practices Summary](#conclusion-and-best-practices-summary)
   - [Architecture Best Practices](#architecture-best-practices)
   - [Development Best Practices](#development-best-practices)
   - [Integration Best Practices](#integration-best-practices-1)
   - [Keeping Up to Date](#keeping-up-to-date)
   - [Final Considerations](#final-considerations)

## Executive Summary

### Quick Start

**Create Real Estate Organization:**
```bash
# Create real estate organization via Organization module
curl -X POST http://realestate.localhost/graphql \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { organization { id name extensionData } success message } }",
    "variables": {
      "input": {
        "name": "Imobiliária Excellence",
        "cnpj": "12345678901234",
        "email": "contato@excellence.com.br",
        "extensionData": {
          "realEstate": {
            "creci": "12345",
            "state_registration": "123.456.789.000"
          }
        }
      }
    }
  }'
```

**Query Real Estate Data:**
```bash
# Query organization with real estate extension data
curl -X POST http://realestate.localhost/graphql \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query GetOrganization($id: ID!) { organization(id: $id) { id name extensionData addresses { street city } } }",
    "variables": { "id": "1" }
  }'
```

**Update Real Estate Data:**
```bash
# Update organization with real estate extension data
curl -X POST http://realestate.localhost/graphql \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "mutation UpdateOrganization($id: ID!, $input: UpdateOrganizationInput!) { updateOrganization(id: $id, input: $input) { id name extensionData } }",
    "variables": {
      "id": "1",
      "input": {
        "extensionData": {
          "realEstate": {
            "creci": "54321",
            "state_registration": "987.654.321.000"
          }
        }
      }
    }
  }'
```

### Key Features

- **🏢 Real Estate Management**: Complete CRUD operations for real estate agencies
- **🔄 Observer Pattern**: Automatic data synchronization with Organization
- **🔌 Extension Data**: Seamless integration with Organization module
- **📊 Complete Queries**: Get organization + real estate data in single request
- **🛡️ Data Integrity**: Transaction-safe operations with rollback support
- **📈 Performance**: Optimized queries with relationship loading
- **🧪 Testable**: Comprehensive error handling

### Architecture Decision

The RealEstate module uses an **Observer pattern integration** where:
- **Creation handled by Organization** with `extensionData`
- **Real estate records automatically created** via event listeners
- **Data accessible through Organization queries** with `extensionData`
- **Direct updates available** through `updateRealEstate` mutation
- **No circular dependencies** between modules

## Authentication

All GraphQL operations in the RealEstate module require authentication. Include the Bearer token in the Authorization header:

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
    "username": "user@example.com",
    "password": "your_password"
  }'
```

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

## Architecture Overview

The RealEstate module follows a modular architecture that integrates with the Organization module through an event-driven Observer Pattern.

### Component Diagram

```
┌─────────────────────┐         ┌─────────────────────┐
│                     │         │                     │
│  Organization API   │         │   RealEstate API    │
│  (GraphQL)          │         │   (GraphQL)         │
│                     │         │                     │
└─────────┬───────────┘         └─────────┬───────────┘
          │                               │
          ▼                               ▼
┌─────────────────────┐         ┌─────────────────────┐
│                     │         │                     │
│  Organization       │         │   RealEstate        │
│  Service            │ ──────► │   Observer          │
│                     │ Events  │                     │
└─────────┬───────────┘         └─────────┬───────────┘
          │                               │
          ▼                               ▼
┌─────────────────────┐         ┌─────────────────────┐
│                     │         │                     │
│  Organization       │         │   RealEstate        │
│  Repository         │         │   Repository        │
│                     │         │                     │
└─────────┬───────────┘         └─────────┬───────────┘
          │                               │
          ▼                               ▼
┌───────────────────────────────────────────────────────┐
│                                                       │
│                      Database                         │
│                                                       │
└───────────────────────────────────────────────────────┘
```

### Data Flow

1. **Create Flow**:
   - Client sends Organization creation mutation with RealEstate extension data
   - Organization is created in the database
   - Organization module fires OrganizationCreatedEvent
   - RealEstateObserver handles the event and extracts RealEstate data
   - RealEstate record is created in the database

2. **Update Flow**:
   - Client sends Organization update mutation with RealEstate extension data
   - Organization is updated in the database
   - Organization module fires OrganizationUpdatedEvent
   - RealEstateObserver handles the event and extracts RealEstate data
   - RealEstate record is updated in the database

3. **Query Flow**:
   - Client sends query for Organization with extensionData
   - Organization is retrieved from the database
   - RealEstate extension data is included in the response

### Schema Structure

The RealEstate module extends the GraphQL schema with:

```graphql
# In modules/RealEstate/GraphQL/schema.graphql

extend type Organization @guard {
  realEstateExtension: RealEstateExtension @field(resolver: "RealEstateExtensionQuery")
}

type RealEstateExtension {
  creci: String! # Can be any string value
  state_registration: String
  broker_count: Int
  year_founded: Int
  accreditations: [String]
}

input RealEstateExtensionInput {
  creci: String! # Can be any string value
  state_registration: String
  broker_count: Int
  year_founded: Int
  accreditations: [String]
}

extend type Mutation {
  updateRealEstateExtension(
    id: ID!, 
    input: RealEstateExtensionInput!
  ): Organization @guard(with: ["api"]) @field(resolver: "UpdateRealEstateExtensionMutation")
}
```

## Integration with Organization Module

### Using extensionData

The RealEstate module's integration with Organization relies on the `extensionData` field, which is a JSON object that can store module-specific data:

```graphql
type Organization {
  id: ID!
  name: String!
  # Other organization fields...
  extensionData: JSON
}
```

### Creating Real Estate Organizations

To create a real estate organization, use the `createOrganization` mutation and include RealEstate-specific data in the `extensionData.realEstate` field:

```graphql
mutation CreateRealEstateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    organization {
      id
      name
      extensionData
    }
    success
    message
  }
}
```

Variables:
```json
{
  "input": {
    "name": "Century 21 Brazil",
    "cnpj": "12345678901234",
    "email": "contact@century21brazil.com.br",
    "phone": "+55 11 98765-4321",
    "extensionData": {
      "realEstate": {
        "creci": "12345",
        "state_registration": "123.456.789.000",
        "broker_count": 42,
        "year_founded": 1998,
        "accreditations": ["COFECI", "SECOVI"]
      }
    },
    "address": {
      "street": "Av. Paulista, 1000",
      "city": "São Paulo",
      "state": "SP",
      "postalCode": "01310-100",
      "country": "Brazil"
    }
  }
}
```

Response:
```json
{
  "data": {
    "createOrganization": {
      "organization": {
        "id": "1",
        "name": "Century 21 Brazil",
        "extensionData": {
          "realEstate": {
            "creci": "12345",
            "state_registration": "123.456.789.000",
            "broker_count": 42,
            "year_founded": 1998,
            "accreditations": ["COFECI", "SECOVI"]
          }
        }
      },
      "success": true,
      "message": "Organization created successfully"
    }
  }
}
```

### Querying Real Estate Organizations

To query a real estate organization, use the `organization` query and include `extensionData` in your selection:

```graphql
query GetRealEstateOrganization($id: ID!) {
  organization(id: $id) {
    id
    name
    cnpj
    email
    phone
    extensionData
    addresses {
      street
      city
      state
      postalCode
      country
    }
  }
}
```

Variables:
```json
{
  "id": "1"
}
```

Response:
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Century 21 Brazil",
      "cnpj": "12345678901234",
      "email": "contact@century21brazil.com.br",
      "phone": "+55 11 98765-4321",
      "extensionData": {
        "realEstate": {
          "creci": "12345",
          "state_registration": "123.456.789.000",
          "broker_count": 42,
          "year_founded": 1998,
          "accreditations": ["COFECI", "SECOVI"]
        }
      },
      "addresses": [
        {
          "street": "Av. Paulista, 1000",
          "city": "São Paulo",
          "state": "SP",
          "postalCode": "01310-100",
          "country": "Brazil"
        }
      ]
    }
  }
}
```

### Updating Real Estate Organizations

To update a real estate organization, use the `updateOrganization` mutation:

```graphql
mutation UpdateRealEstateOrganization($id: ID!, $input: UpdateOrganizationInput!) {
  updateOrganization(id: $id, input: $input) {
    organization {
      id
      name
      extensionData
    }
    success
    message
  }
}
```

Variables:
```json
{
  "id": "1",
  "input": {
    "name": "Century 21 Brazil - São Paulo",
    "extensionData": {
      "realEstate": {
        "creci": "12345",
        "state_registration": "123.456.789.000",
        "broker_count": 50,
        "year_founded": 1998,
        "accreditations": ["COFECI", "SECOVI"]
      }
    }
  }
}
```

Response:
```json
{
  "data": {
    "updateOrganization": {
      "organization": {
        "id": "1",
        "name": "Century 21 Brazil - São Paulo",
        "extensionData": {
          "realEstate": {
            "creci": "12345",
            "state_registration": "123.456.789.000",
            "broker_count": 50,
            "year_founded": 1998,
            "accreditations": ["COFECI", "SECOVI"]
          }
        }
      },
      "success": true,
      "message": "Organization updated successfully"
    }
  }
}
```

### Direct RealEstate Extension Updates

For targeted updates to just the RealEstate extension data, you can use the dedicated mutation:

```graphql
mutation UpdateRealEstateExtension($id: ID!, $input: RealEstateExtensionInput!) {
  updateRealEstateExtension(id: $id, input: $input) {
    id
    name
    extensionData
  }
}
```

Variables:
```json
{
  "id": "1",
  "input": {
    "creci": "CRECI/SP 12345-J",
    "broker_count": 55,
    "accreditations": ["COFECI", "CRECISP", "SECOVI", "ABMI"]
  }
}
```

Response:
```json
{
  "data": {
    "updateRealEstateExtension": {
      "id": "1",
      "name": "Century 21 Brazil - São Paulo",
      "extensionData": {
        "realEstate": {
          "creci": "CRECI/SP 12345-J",
          "state_registration": "123.456.789.000",
          "broker_count": 55,
          "year_founded": 1998,
          "accreditations": ["COFECI", "CRECISP", "SECOVI", "ABMI"]
        }
      }
    }
  }
}
```

### Filtering Organizations by RealEstate Extension Data

You can filter organizations based on RealEstate extension data:

```graphql
query FilterRealEstateOrganizations($filter: OrganizationFilterInput!) {
  organizations(filter: $filter) {
    data {
      id
      name
      extensionData
    }
    paginatorInfo {
      total
      currentPage
      lastPage
    }
  }
}
```

Variables:
```json
{
  "filter": {
    "extensionData": {
      "realEstate": {
        "creci": {
          "contains": "123"
        }
      }
    }
  }
}
```

Response:
```json
{
  "data": {
    "organizations": {
      "data": [
        {
          "id": "1",
          "name": "Century 21 Brazil - São Paulo",
          "extensionData": {
            "realEstate": {
              "creci": "12345",
              "state_registration": "123.456.789.000"
            }
          }
        },
        {
          "id": "3",
          "name": "RE/MAX São Paulo",
          "extensionData": {
            "realEstate": {
              "creci": "98765",
              "state_registration": "987.654.321.000"
            }
          }
        }
      ],
      "paginatorInfo": {
        "total": 2,
        "currentPage": 1,
        "lastPage": 1
      }
    }
  }
}
```

## Conclusion and Best Practices Summary

### Architecture Best Practices

1. **Use the Observer Pattern**:
   - Always use the established Observer Pattern for module integration
   - Avoid direct dependencies between modules
   - Follow the example of RealEstate extension implementation for new extensions

2. **Extension Data Structure**:
   - Store module-specific data under a dedicated namespace in extensionData
   - Example: `extensionData.realEstate` for RealEstate module data
   - Follow the established naming conventions for consistency

3. **Schema First Development**:
   - Define GraphQL schema types before implementing resolvers
   - Use schema directives for common operations like authentication and authorization
   - Extend existing types rather than creating duplicate types

### Development Best Practices

1. **Error Handling**:
   - Implement comprehensive error handling for all GraphQL operations
   - Parse error categories from the extensions.category field
   - Provide user-friendly error messages

2. **Testing**:
   - Write unit tests for all resolvers and validators
   - Use feature tests to verify end-to-end functionality
   - Mock external dependencies to ensure test isolation

### Integration Best Practices

1. **Authentication and Authorization**:
   - Always include the Authorization header with Bearer token
   - Verify user permissions match the required roles
   - Handle authentication errors by redirecting to login

2. **GraphQL Operations**:
   - Use named operations for better error messages
   - Define reusable fragments for common fields
   - Include only the fields you need in your queries

3. **Error Handling**:
   - Check for errors array in GraphQL responses
   - Handle specific error categories appropriately
   - Provide helpful error messages to users

### Keeping Up to Date

The RealEstate module is continuously evolving. Stay up to date with changes:

1. **Check Documentation**: Review this documentation regularly for updates
2. **Use GraphQL Playground**: Explore the latest schema in GraphQL Playground
3. **Read Migration Guides**: Follow migration guides when upgrading

### Final Considerations

The RealEstate module's integration with the Organization module via the Observer Pattern demonstrates the power of decoupled architecture in building maintainable and extensible applications. This approach allows modules to evolve independently while still working together harmoniously.

When implementing new features or integrating with the RealEstate module, always follow the established patterns and best practices outlined in this documentation. By doing so, you'll ensure compatibility, maintainability, and a consistent developer experience.

---

*This documentation was last updated: April 2025*

*For questions or support, contact the development team at dev@thedevkitchen.com.br*
