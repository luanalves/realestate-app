# RealEstate Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL mutations available in the RealEstate module and how to access real estate data through the Organization module's extension system.

**Important**: The RealEstate module integrates with the Organization module's extension system. For **reading** real estate data, use the Organization module's queries with `extensionData`. This document covers both **creating real estate agencies** and **accessing data via Organization extension system**.

## Table of Contents

1. [Introduction](#introduction)
2. [Authentication](#authentication)
3. [How to Create a Real Estate Organization](#how-to-create-a-real-estate-organization)
4. [How to Update a Real Estate Organization](#como-atualizar-uma-organização-imobiliária)
5. [Integration with Organization Module](#integration-with-organization-module)
6. [Extension Data Examples](#extension-data-examples)
7. [RealEstate Mutations](#realestate-mutations)
8. [Error Handling](#error-handling)
9. [Complete Examples](#complete-examples)

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

## Como Criar uma Organização Imobiliária - Exemplos Atualizados

### 🏢 **Exemplo 1: Formato CRECI Oficial Completo**

```bash
curl --location 'http://realestate.localhost/graphql' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
--data-raw '{
  "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { organization { id name fantasy_name cnpj description email phone website active created_at updated_at extensionData addresses { id street number complement neighborhood city state zip_code country type active created_at updated_at } } success message } }",
  "variables": {
    "input": {
      "name": "Imobiliária Excellence Premium",
      "fantasy_name": "Excellence Premium",
      "cnpj": "11222333444555",
      "description": "Imobiliária especializada em imóveis de alto padrão",
      "email": "contato@excellencepremium.com.br",
      "phone": "+55 11 88888-7777",
      "website": "https://www.excellencepremium.com.br",
      "active": true,
      "address": {
        "street": "Rua Oscar Freire",
        "number": "1500",
        "complement": "Loja 1",
        "neighborhood": "Jardins",
        "city": "São Paulo",
        "state": "SP",
        "zip_code": "01426000",
        "country": "BR",
        "type": "headquarters"
      },
      "extensionData": {
        "realEstate": {
          "creci": "CRECI/SP 98765-J",
          "state_registration": "987.654.321.000"
        }
      }
    }
  }
}'
```

### 🏢 **Exemplo 2: Formato CRECI Simplificado**

```bash
curl --location 'http://realestate.localhost/graphql' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
--data-raw '{
  "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { organization { id name fantasy_name extensionData } success message } }",
  "variables": {
    "input": {
      "name": "Imobiliária Teste Simplificada",
      "fantasy_name": "Teste Simples",
      "cnpj": "55666777888999",
      "description": "Teste com CRECI simplificado",
      "email": "teste@simplificada.com.br",
      "phone": "+55 11 77777-6666",
      "website": "https://www.testesimples.com.br",
      "active": true,
      "extensionData": {
        "realEstate": {
          "creci": "54321-J",
          "state_registration": "555.666.777.888"
        }
      }
    }
  }
}'
```

### 📋 **Formatos de CRECI Aceitos**

| Formato | Exemplo | Descrição |
|---------|---------|-----------|
| **Oficial Completo** | `CRECI/SP 12345-J` | Formato recomendado conforme regulamentação |
| **Oficial com outros estados** | `CRECI/RJ 98765-J` | Qualquer UF brasileira |
| **Simplificado** | `54321-J` | Formato sem prefixo |
| **Compatibilidade** | `J-12345` | Mantido para compatibilidade |

### ⚠️ **Regras Importantes**

1. **Pessoa Jurídica**: Empresas devem usar `-J`
2. **Pessoa Física**: Corretores individuais usam `-F`
3. **Tamanho**: 4-6 dígitos para o número
4. **Estados**: Qualquer UF brasileira válida
5. **CEP**: Máximo 8 caracteres (sem hífen)

### ✅ **Resposta Esperada**

```json
{
  "data": {
    "createOrganization": {
      "organization": {
        "id": "23",
        "name": "Imobiliária Excellence Premium",
        "fantasy_name": "Excellence Premium",
        "cnpj": "11222333444555",
        "description": "Imobiliária especializada em imóveis de alto padrão",
        "email": "contato@excellencepremium.com.br",
        "phone": "+55 11 88888-7777",
        "website": "https://www.excellencepremium.com.br",
        "active": true,
        "created_at": "2025-07-06 23:55:20",
        "updated_at": "2025-07-06 23:55:20",
        "extensionData": "{\"realEstate\":{\"id\":12,\"creci\":\"CRECI/SP 98765-J\",\"state_registration\":\"987.654.321.000\",\"created_at\":\"2025-07-06T23:55:20.000000Z\",\"updated_at\":\"2025-07-06T23:55:20.000000Z\"}}",
        "addresses": [
          {
            "id": "11",
            "street": "Rua Oscar Freire",
            "number": "1500",
            "complement": "Loja 1",
            "neighborhood": "Jardins",
            "city": "São Paulo",
            "state": "SP",
            "zip_code": "01426000",
            "country": "BR",
            "type": "headquarters",
            "active": true,
            "created_at": "2025-07-06 23:55:20",
            "updated_at": "2025-07-06 23:55:20"
          }
        ]
      },
      "success": true,
      "message": "Organization created successfully"
    }
  }
}
```

### ❌ **Exemplos de Erros**

#### CRECI de Pessoa Física (Não Permitido para Empresas)
```json
{
  "extensionData": {
    "realEstate": {
      "creci": "CRECI/SP 12345-F"  // ❌ ERRO: Use -J para empresas
    }
  }
}
```

**Erro retornado:**
```json
{
  "errors": [
    {
      "message": "Failed to create RealEstate extension: CRECI for real estate companies must end with -J (pessoa jurídica). Example: CRECI/SP 12345-J or 12345-J"
    }
  ]
}
```

#### Formato Inválido
```json
{
  "extensionData": {
    "realEstate": {
      "creci": "12345"  // ❌ ERRO: Falta categoria (-J ou -F)
    }
  }
}
```

**Erro retornado:**
```json
{
  "errors": [
    {
      "message": "Failed to create RealEstate extension: Invalid CRECI format. Expected formats: CRECI/SP 12345-J, 12345-J, or J-12345. Use F for pessoa física or J for pessoa jurídica."
    }
  ]
}
```

### Step 1: Prepare Your Data

Before creating a real estate organization, you need to gather the following information:

**Organization Data:**
- `name`: Legal name of the real estate company
- `fantasy_name`: Commercial name (optional)
- `cnpj`: Brazilian tax identification number
- `description`: Company description
- `email`: Primary contact email
- `phone`: Primary contact phone
- `website`: Company website (optional)
- `active`: Whether the organization is active (default: true)

**Real Estate Specific Data:**
- `creci`: CRECI registration number (required for real estate agencies in Brazil)
  - **Formato Oficial Completo**: `CRECI/SP 12345-J` (recomendado)
  - **Formato Simplificado**: `12345-J`
  - **Formato Compatibilidade**: `J-12345`
  - **Categoria**: Use `-J` para pessoa jurídica (empresas) ou `-F` para pessoa física
  - **Estados**: Qualquer UF brasileira (SP, RJ, MG, etc.)
- `state_registration`: State registration number

**Address Data (optional):**
- `street`: Street name
- `number`: Street number
- `complement`: Complement (apartment, suite, etc.)
- `neighborhood`: Neighborhood name
- `city`: City name
- `state`: State code (e.g., "SP", "RJ")
- `zip_code`: Postal code
- `country`: Country code (default: "BR")

### Step 2: Execute the Creation Mutation

Use the `createOrganization` mutation to create the organization:

**GraphQL Mutation:**
```graphql
mutation CreateOrganization($input: CreateOrganizationInput!) {
  createOrganization(input: $input) {
    organization {
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
    success
    message
  }
  }
}
```

**Variables Example:**
```json
{
  "input": {
    "name": "Imobiliária Excellence",
    "fantasy_name": "Excellence Imóveis",
    "cnpj": "12345678901234",
    "description": "Imobiliária especializada em imóveis comerciais e residenciais de alto padrão",
    "email": "contato@excellenceimob.com.br",
    "phone": "+55 11 99999-8888",
    "website": "https://www.excellenceimob.com.br",
    "active": true,
    "address": {
      "street": "Avenida Faria Lima",
      "number": "2000",
      "complement": "Conjunto 1501",
      "neighborhood": "Itaim Bibi",
      "city": "São Paulo",
      "state": "SP",
      "zip_code": "01451000",
      "country": "BR",
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
    "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { organization { id name fantasy_name cnpj description email phone website active created_at updated_at extensionData addresses { id street number complement neighborhood city state zip_code country type active created_at updated_at } } success message } }",
    "variables": {
      "input": {
        "name": "Imobiliária Excellence",
        "fantasy_name": "Excellence Imóveis",
        "cnpj": "12345678901234",
        "description": "Imobiliária especializada em imóveis comerciais e residenciais de alto padrão",
        "email": "contato@excellenceimob.com.br",
        "phone": "+55 11 99999-8888",
        "website": "https://www.excellenceimob.com.br",
        "active": true,
        "address": {
          "street": "Avenida Faria Lima",
          "number": "2000",
          "complement": "Conjunto 1501",
          "neighborhood": "Itaim Bibi",
          "city": "São Paulo",
          "state": "SP",
          "zip_code": "01451000",
          "country": "BR"
        }
      }
    }
  }'
```

**Expected Response:**
```json
{
  "data": {
    "createOrganization": {
      "organization": {
        "id": "1",
        "name": "Imobiliária Excellence",
        "fantasy_name": "Excellence Imóveis",
        "cnpj": "12345678901234",
        "description": "Imobiliária especializada em imóveis comerciais e residenciais de alto padrão",
        "email": "contato@excellenceimob.com.br",
        "phone": "+55 11 99999-8888",
        "website": "https://www.excellenceimob.com.br",
        "active": true,
        "created_at": "2024-01-15T10:30:00Z",
        "updated_at": "2024-01-15T10:30:00Z",
        "extensionData": "[]",
        "addresses": [
          {
            "id": "1",
            "street": "Avenida Faria Lima",
            "number": "2000",
            "complement": "Conjunto 1501",
            "neighborhood": "Itaim Bibi",
            "city": "São Paulo",
            "state": "SP",
            "zip_code": "01451000",
            "country": "BR",
            "type": "headquarters",
            "active": true,
            "created_at": "2024-01-15T10:30:00Z",
            "updated_at": "2024-01-15T10:30:00Z"
          }
        ]
      },
      "success": true,
      "message": "Organization created successfully"
    }
  }
}
```

### Step 3: Create the Real Estate Extension (Optional)

If you want to add specific real estate data (like CRECI), you can create a RealEstate record manually using the service:

**Note**: This step is optional and depends on your business requirements. The organization can function without the RealEstate extension, but the `extensionData` field will be empty.

### Step 4: Verify the Organization was Created

After creation, you can verify the organization was properly created by querying it through the Organization module with extension data:

**Query:**
```graphql
query VerifyCreatedOrganization($id: ID!) {
  organization(id: $id) {
    id
    name
    fantasy_name
    cnpj
    email
    phone
    website
    active
    extensionData  # This will contain the real estate specific data
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

**Expected Response:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Imobiliária Excellence",
      "fantasy_name": "Excellence Imóveis",
      "cnpj": "12345678901234",
      "email": "contato@excellenceimob.com.br",
      "phone": "+55 11 99999-8888",
      "website": "https://www.excellenceimob.com.br",
      "active": true,
      "extensionData": {
        "realEstate": {
          "id": "1",
          "creci": "J-12345",
          "state_registration": "123.456.789.000",
          "created_at": "2024-01-15T10:30:00Z",
          "updated_at": "2024-01-15T10:30:00Z"
        }
      },
      "addresses": [
        {
          "id": "1",
          "street": "Avenida Faria Lima",
          "number": "2000",
          "complement": "Conjunto 1501",
          "neighborhood": "Itaim Bibi",
          "city": "São Paulo",
          "state": "SP",
          "zip_code": "01451000",
          "country": "BR",
          "type": "headquarters",
          "active": true
        }
      ]
    }
  }
}
```

### Important Notes

1. **Standard Organization Creation**: Use the `createOrganization` mutation to create the base organization record.

2. **Extension Data**: Real estate-specific data can be added later via the RealEstate module's services and will be automatically available through the Organization module's `extensionData` field.

3. **Address Creation**: If you provide address data, the system automatically creates the organization's address record.

4. **Modular Architecture**: The system uses a modular approach where Organization handles base data and RealEstate extends it with specific functionality.

5. **Validation**: The system validates all required fields and business rules (e.g., CNPJ format, email format, etc.).

6. **Multi-tenant Support**: The system automatically assigns the correct tenant based on the authenticated user's permissions.

## Integration with Organization Module

The RealEstate module integrates with the Organization module's extension system, allowing you to get complete organization and real estate data in a single query. The recommended approach is to use Organization queries with `extensionData` for reading data.

### Architectural Overview

```
┌─────────────────┐    Observer Pattern    ┌─────────────────┐
│   Organization  │◄──────────────────────│   RealEstate    │
│     Module      │                       │     Module      │
│                 │                       │                 │
│ - Generic CRUD  │                       │ - Extends Org   │
│ - Address Mgmt  │                       │ - CRECI Data    │
│ - Member Mgmt   │                       │ - State Reg     │
│ - extensionData │                       │ - Validations   │
└─────────────────┘                       └─────────────────┘
```

### How Extension Data Works

1. **Create**: Use Organization `createOrganization` mutation to create the base organization
2. **Extend**: Optionally create RealEstate records via RealEstate module services
3. **Read**: Use Organization queries - `extensionData` automatically populated when RealEstate data exists
4. **Update**: Use RealEstate mutations to update both records
5. **Delete**: Use Organization delete - RealEstate record auto-deleted (CASCADE)

## Extension Data Examples

The `extensionData` field in the Organization module provides access to real estate-specific data without needing to directly query the RealEstate module. This field is automatically populated when an organization has real estate data associated with it.

### Understanding Extension Data Structure

The `extensionData` field contains a nested structure with module-specific data:

```json
{
  "extensionData": {
    "realEstate": {
      "id": "1",
      "creci": "J-12345",
      "state_registration": "123.456.789.000",
      "created_at": "2024-01-15T10:30:00Z",
      "updated_at": "2024-01-15T10:30:00Z"
    }
  }
}
```

**Key Points:**
- `extensionData` is always a JSON object
- Real estate data is nested under the `realEstate` key
- Contains all real estate-specific fields
- Returns `null` if the organization is not a real estate agency

### Example 1: Get Single Organization with Real Estate Data

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
    created_at
    updated_at
    extensionData  # Contains real estate specific data
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
    }
    members {
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
    "query": "query GetOrganizationWithRealEstate($id: $id) { organization(id: $id) { id name fantasy_name cnpj description email phone website active created_at updated_at extensionData addresses { id street number complement neighborhood city state zip_code country type active } } }",
    "variables": {
      "id": "1"
    }
  }'
```

**Response Example:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Imobiliária ABC",
      "fantasy_name": "ABC Imóveis",
      "cnpj": "12345678901234",
      "description": "Imobiliária especializada em imóveis residenciais",
      "email": "contato@abc.com",
      "phone": "+55 11 99999-9999",
      "website": "https://abc.com",
      "active": true,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z",
      "extensionData": {
        "realEstate": {
          "id": "1",
          "creci": "J-12345",
          "state_registration": "123.456.789.000",
          "created_at": "2024-01-01T00:00:00Z",
          "updated_at": "2024-01-01T00:00:00Z"
        }
      },
      "addresses": [
        {
          "id": "1",
          "street": "Avenida Paulista",
          "number": "1000",
          "city": "São Paulo",
          "state": "SP",
          "zip_code": "01310-100",
          "country": "BR",
          "type": "headquarters",
          "active": true
        }
      ]
    }
  }
}
```

### Example 2: List All Organizations (Including Real Estate Agencies)

This query returns all organizations, and those that are real estate agencies will have their `extensionData` populated:

**Query:**
```graphql
query GetAllOrganizations($first: Int!, $page: Int) {
  organizations(first: $first, page: $page) {
    data {
      id
      name
      fantasy_name
      cnpj
      email
      phone
      website
      active
      extensionData  # Will contain realEstate data if applicable
      addresses {
        id
        street
        number
        city
        state
        type
        active
      }
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

**Response Example:**
```json
{
  "data": {
    "organizations": {
      "data": [
        {
          "id": "1",
          "name": "Imobiliária Excellence",
          "fantasy_name": "Excellence Imóveis",
          "cnpj": "12345678901234",
          "email": "contato@excellenceimob.com.br",
          "phone": "+55 11 99999-8888",
          "website": "https://www.excellenceimob.com.br",
          "active": true,
          "extensionData": {
            "realEstate": {
              "id": "1",
              "creci": "J-12345",
              "state_registration": "123.456.789.000",
              "created_at": "2024-01-15T10:30:00Z",
              "updated_at": "2024-01-15T10:30:00Z"
            }
          },
          "addresses": [
            {
              "id": "1",
              "street": "Avenida Faria Lima",
              "number": "2000",
              "city": "São Paulo",
              "state": "SP",
              "zip_code": "01451000",
              "type": "headquarters",
              "active": true
            }
          ]
        },
        {
          "id": "2",
          "name": "Consultoria ABC",
          "fantasy_name": "ABC Consulting",
          "cnpj": "98765432109876",
          "email": "contato@abcconsulting.com.br",
          "phone": "+55 11 88888-7777",
          "website": "https://www.abcconsulting.com.br",
          "active": true,
          "extensionData": null,  # Not a real estate agency
          "addresses": [
            {
              "id": "2",
              "street": "Rua das Flores",
              "number": "500",
              "city": "Rio de Janeiro",
              "state": "RJ",
              "type": "headquarters",
              "active": true
            }
          ]
        }
      ],
      "paginatorInfo": {
        "count": 2,
        "currentPage": 1,
        "firstItem": 1,
        "hasMorePages": false,
        "lastItem": 2,
        "lastPage": 1,
        "perPage": 10,
        "total": 2
      }
    }
  }
}
```

### Example 3: Filter Organizations by Real Estate Status

You can create custom queries to filter organizations based on whether they have real estate data:

**Query for Real Estate Agencies Only:**
```graphql
query GetRealEstateAgencies($first: Int!, $page: Int) {
  organizations(first: $first, page: $page) {
    data {
      id
      name
      fantasy_name
      cnpj
      email
      phone
      extensionData
      addresses {
        street
        number
        city
        state
      }
    }
    paginatorInfo {
      count
      currentPage
      lastPage
      total
    }
  }
}
```

**Client-side Filtering Example (JavaScript):**
```javascript
// Filter results to show only real estate agencies
const realEstateAgencies = organizationsData.data.filter(org => 
  org.extensionData && org.extensionData.realEstate
);

// Extract real estate specific data
const realEstateData = realEstateAgencies.map(org => ({
  organizationId: org.id,
  name: org.name,
  creci: org.extensionData.realEstate.creci,
  stateRegistration: org.extensionData.realEstate.state_registration,
  email: org.email,
  phone: org.phone,
  address: org.addresses[0] // Primary address
}));
```

### Example 4: Using Extension Data for Business Logic

**Client-side Example (JavaScript):**
```javascript
// Function to check if an organization is a real estate agency
function isRealEstateAgency(organization) {
  return organization.extensionData && 
         organization.extensionData.realEstate &&
         organization.extensionData.realEstate.creci;
}

// Function to get real estate details
function getRealEstateDetails(organization) {
  if (!isRealEstateAgency(organization)) {
    return null;
  }
  
  return {
    id: organization.extensionData.realEstate.id,
    creci: organization.extensionData.realEstate.creci,
    stateRegistration: organization.extensionData.realEstate.state_registration,
    createdAt: organization.extensionData.realEstate.created_at,
    updatedAt: organization.extensionData.realEstate.updated_at
  };
}

// Example usage
const organization = /* ... organization data from query ... */;

if (isRealEstateAgency(organization)) {
  const realEstateDetails = getRealEstateDetails(organization);
  console.log(`CRECI: ${realEstateDetails.creci}`);
  console.log(`State Registration: ${realEstateDetails.stateRegistration}`);
}
```

### Example 5: Complete Organization Profile with Real Estate Data

**Query:**
```graphql
query GetCompleteOrganizationProfile($id: ID!) {
  organization(id: $id) {
    # Basic organization data
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
    
    # Real estate specific data (if applicable)
    extensionData
    
    # Address information
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
    
    # Team members
    members {
      id
      role
      position
      is_active
      joined_at
      user {
        id
        name
        email
        phone
        profile {
          avatar_url
          bio
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

**Complete Response Example:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Imobiliária Excellence",
      "fantasy_name": "Excellence Imóveis",
      "cnpj": "12345678901234",
      "description": "Imobiliária especializada em imóveis comerciais e residenciais de alto padrão",
      "email": "contato@excellenceimob.com.br",
      "phone": "+55 11 99999-8888",
      "website": "https://www.excellenceimob.com.br",
      "active": true,
      "created_at": "2024-01-15T10:30:00Z",
      "updated_at": "2024-01-15T10:30:00Z",
      "extensionData": {
        "realEstate": {
          "id": "1",
          "creci": "J-12345",
          "state_registration": "123.456.789.000",
          "created_at": "2024-01-15T10:30:00Z",
          "updated_at": "2024-01-15T10:30:00Z"
        }
      },
      "addresses": [
        {
          "id": "1",
          "street": "Avenida Faria Lima",
          "number": "2000",
          "complement": "Conjunto 1501",
          "neighborhood": "Itaim Bibi",
          "city": "São Paulo",
          "state": "SP",
          "zip_code": "01451000",
          "country": "BR",
          "type": "headquarters",
          "active": true,
          "created_at": "2024-01-15T10:30:00Z",
          "updated_at": "2024-01-15T10:30:00Z"
        }
      ],
      "members": [
        {
          "id": "1",
          "role": "owner",
          "position": "CEO",
          "is_active": true,
          "joined_at": "2024-01-15T10:30:00Z",
          "user": {
            "id": "1",
            "name": "João Silva",
            "email": "joao.silva@excellenceimob.com.br",
            "phone": "+55 11 99999-9999",
            "profile": {
              "avatar_url": "https://example.com/avatars/joao.jpg",
              "bio": "CEO da Excellence Imóveis com 15 anos de experiência no mercado imobiliário"
            }
          }
        }
      ]
    }
  }
}
```

### Key Benefits of Using Extension Data

1. **Single Query**: Get both organization and real estate data in one request
2. **Automatic Population**: Extension data is automatically populated by the system
3. **Flexible Structure**: Can accommodate different types of organization extensions
4. **Performance**: Reduces the need for multiple API calls
5. **Consistency**: Always returns the same data structure regardless of the organization type

### List Organizations with Real Estate Extensions

**Query:**
```graphql
query GetOrganizationsWithRealEstate($first: Int!, $page: Int) {
  organizations(first: $first, page: $page) {
    data {
      id
      name
      fantasy_name
      cnpj
      email
      phone
      website
      active
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

**Response Example:**
```json
{
  "data": {
    "organizations": {
      "data": [
        {
          "id": "1",
          "name": "Imobiliária ABC",
          "fantasy_name": "ABC Imóveis",
          "cnpj": "12345678901234",
          "email": "contato@abc.com",
          "phone": "+55 11 99999-9999",
          "website": "https://abc.com",
          "active": true,
          "extensionData": {
            "realEstate": {
              "id": "1",
              "creci": "J-12345",
              "state_registration": "123.456.789.000"
            }
          }
        },
        {
          "id": "2",
          "name": "Consultoria XYZ",
          "fantasy_name": "XYZ Consulting",
          "cnpj": "98765432109876",
          "email": "contato@xyz.com",
          "phone": "+55 11 88888-8888",
          "website": "https://xyz.com",
          "active": true,
          "extensionData": null
        }
      ],
      "paginatorInfo": {
        "count": 2,
        "currentPage": 1,
        "firstItem": 1,
        "hasMorePages": false,
        "lastItem": 2,
        "lastPage": 1,
        "perPage": 10,
        "total": 2
      }
    }
  }
}
```

### Benefits of Using Organization Extension System

1. **✅ Complete Data**: Get both organization and real estate data in one query
2. **✅ Addresses**: Access organization addresses in the same query  
3. **✅ Members**: Access organization members and their roles
4. **✅ Flexibility**: The system supports multiple organization types
5. **✅ Performance**: Efficient data loading with proper relationships
6. **✅ Consistency**: Unified interface for all organization types

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
      "zip_code": "01000000",
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
    "variables": { "input": { "name": "Nova Imobiliária", "fantasy_name": "Nova Imob", "cnpj": "98765432109876", "description": "Descrição da nova imobiliária", "email": "contato@novaimob.com", "phone": "11988888888", "website": "https://www.novaimob.com", "creci": "CRECI/SP 54321-J", "state_registration": "987654321", "active": true, "address": { "street": "Avenida Principal", "number": "1000", "complement": "Sala 101", "neighborhood": "Centro", "city": "São Paulo", "state": "SP", "zip_code": "01000000", "country": "BR" } } }
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

### 🎯 **Quando Usar Cada Abordagem**

#### Use updateOrganization (com extensionData) quando:
- ✅ Quiser atualizar dados básicos da organização E dados específicos da imobiliária juntos
- ✅ Precisar de transação atômica (tudo ou nada)
- ✅ Trabalhar com interface de usuário unificada
- ✅ Garantir consistência entre organização e extensões

#### Use updateRealEstate quando:
- ✅ Quiser atualizar APENAS dados específicos da imobiliária
- ✅ Trabalhar com interfaces específicas do módulo RealEstate
- ✅ Fazer atualizações em lote apenas de dados imobiliários

### 🔄 **Fluxo de Atualização Recomendado**

1. **Para atualizações gerais**: Use `updateOrganization` com `extensionData`
2. **Para atualizações específicas**: Use `updateRealEstate` diretamente
3. **Para consultas**: Sempre use `organization` query com `extensionData`

### 📝 **Exemplo de Workflow Completo**

```bash
# 1. Criar organização com dados de imobiliária
curl --location 'http://realestate.localhost/graphql' \
--data-raw '{
  "query": "mutation CreateOrganization($input: CreateOrganizationInput!) { createOrganization(input: $input) { organization { id name extensionData } success message } }",
  "variables": {
    "input": {
      "name": "Imobiliária Workflow Test",
      "cnpj": "11111111111111",
      "email": "workflow@test.com",
      "extensionData": {
        "realEstate": {
          "creci": "CRECI/SP 11111-J",
          "state_registration": "111111111"
        }
      }
    }
  }
}'

# 2. Atualizar dados completos (organização + imobiliária)
curl --location 'http://realestate.localhost/graphql' \
--data-raw '{
  "query": "mutation UpdateOrganization($id: ID!, $input: UpdateOrganizationInput!) { updateOrganization(id: $id, input: $input) { id name extensionData } }",
  "variables": {
    "id": "ORGANIZATION_ID",
    "input": {
      "name": "Imobiliária Workflow Test ATUALIZADA",
      "phone": "11999999999",
      "extensionData": {
        "realEstate": {
          "creci": "CRECI/RJ 22222-J",
          "state_registration": "222222222"
        }
      }
    }
  }
}'

# 3. Consultar dados completos
curl --location 'http://realestate.localhost/graphql' \
--data-raw '{
  "query": "query GetOrganization($id: ID!) { organization(id: $id) { id name phone extensionData } }",
  "variables": {
    "id": "ORGANIZATION_ID"
  }
}'
```

## 📋 **Resumo da Implementação**

### ✅ **O que foi implementado:**

1. **Evento OrganizationUpdated**: Disparado quando uma organização é atualizada
2. **Listener UpdateRealEstateOnOrganizationUpdatedListener**: Escuta o evento e atualiza dados da imobiliária
3. **Service RealEstateUpdateService**: Valida e atualiza dados específicos da imobiliária
4. **Campo extensionData**: Adicionado ao UpdateOrganizationInput no GraphQL
5. **Validações**: CRECI válido e pessoa jurídica obrigatória para empresas
6. **Transação atômica**: Rollback automático se alguma validação falhar

### 🔄 **Fluxo de funcionamento:**

```
updateOrganization(extensionData) 
    ↓
UpdateOrganizationResolver extrai extensionData
    ↓
OrganizationService.updateOrganization() atualiza dados básicos
    ↓
Event::dispatch(OrganizationUpdated) com extensionData
    ↓
UpdateRealEstateOnOrganizationUpdatedListener escuta evento
    ↓
RealEstateUpdateService.updateFromOrganization() valida e atualiza
    ↓
Transação commitada ou revertida em caso de erro
```

### 🎯 **Vantagens desta implementação:**

- **Consistência**: Dados sempre atualizados em conjunto
- **Atomicidade**: Tudo ou nada - não há estados inconsistentes
- **Flexibilidade**: Funciona com ou sem extensionData
- **Desacoplamento**: Módulos não dependem diretamente um do outro
- **Validação**: Regras de negócio aplicadas automaticamente
- **Performance**: Uma única transação para todas as atualizações
