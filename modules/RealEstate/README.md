# RealEstate Module

This module handles real estate agencies management in the application. It provides functionality to create, update, delete, and manage real estate agencies.

## Features

- Create real estate agencies
- Update real estate agency information
- Delete real estate agencies
- List real estate agencies
- Search real estate agencies by ID
- Multi-tenant support (agencies are isolated by tenant)

## Structure

- **Database/**: Migrations and seeders
- **GraphQL/**: 
  - **schema.graphql**: GraphQL schema definitions for this module
  - **Mutations/**: GraphQL mutation resolvers
  - **Queries/**: GraphQL query resolvers
- **Models/**: Eloquent models
- **Providers/**: Service providers
- **Services/**: Business logic services
- **Tests/**: Unit and feature tests

## GraphQL Operations

Este módulo segue a abordagem modular para GraphQL, conforme definido no [ADR-008](/doc/architectural-decision-records/008-graphql-module-pattern.md). As definições de tipos GraphQL estão no arquivo `GraphQL/schema.graphql` do módulo, e são importadas automaticamente no esquema principal da aplicação.

### Queries

#### 1. `realEstates` - Listar Imobiliárias

**Descrição:** Retorna uma lista paginada de imobiliárias (agências).

**Parâmetros:**
- `first: Int = 10` - Número de itens por página (padrão: 10)
- `page: Int = 1` - Número da página (padrão: 1)
- `orderBy: [OrderByClause!]` - Opções de ordenação (opcional)

**Retorno:** `RealEstatePaginator!` - Paginador contendo lista de imobiliárias

**Exemplo de chamada:**
```graphql
query {
  realEstates(first: 5, page: 1, orderBy: [{column: "name", order: ASC}]) {
    data {
      id
      name
      email
      phone
    }
    paginatorInfo {
      currentPage
      lastPage
      total
    }
  }
}
```

#### 2. `realEstateById` - Buscar Imobiliária por ID

**Descrição:** Retorna uma imobiliária específica pelo seu ID.

**Parâmetros:**
- `id: ID!` - ID da imobiliária (obrigatório)

**Retorno:** `RealEstate` - Detalhes de uma imobiliária

**Exemplo de chamada:**
```graphql
query {
  realEstateById(id: 1) {
    id
    name
    email
    phone
    website
    address {
      street
      city
      state
      zip_code
      country
    }
  }
}
```

### Mutations

#### 1. `createRealEstate` - Criar Nova Imobiliária

**Descrição:** Cria uma nova imobiliária no sistema.

**Parâmetros:**
- `name: String!` - Nome da imobiliária (obrigatório)
- `fantasy_name: String` - Nome fantasia (opcional)
- `corporate_name: String` - Razão social (opcional)
- `cnpj: String!` - CNPJ da imobiliária (obrigatório, único)
- `description: String` - Descrição da imobiliária (opcional)
- `email: String!` - Email de contato (obrigatório, único)
- `phone: String` - Telefone de contato (opcional)
- `website: String` - Site da imobiliária (opcional)
- `creci: String` - Número do CRECI (opcional)
- `state_registration: String` - Inscrição estadual (opcional)
- `legal_representative: String` - Representante legal (opcional)
- `active: Boolean = true` - Status ativo/inativo (padrão: true)
- `address: RealEstateAddressInput` - Informações de endereço (opcional)

**Retorno:** `RealEstate!` - A imobiliária recém-criada

**Exemplo de chamada:**
```graphql
mutation {
  createRealEstate(
    name: "Imobiliária Exemplo"
    cnpj: "12345678901234"
    email: "contato@exemplo.com"
    phone: "1199998888"
    website: "https://www.exemplo.com"
    creci: "12345"
    address: {
      street: "Avenida Paulista, 1000"
      city: "São Paulo"
      state: "SP"
      zip_code: "01310-100"
      country: "Brasil"
    }
  ) {
    id
    name
    email
  }
}
```

#### 2. `updateRealEstate` - Atualizar Imobiliária Existente

**Descrição:** Atualiza os dados de uma imobiliária existente.

**Parâmetros:**
- `id: ID!` - ID da imobiliária a ser atualizada (obrigatório)
- `input: UpdateRealEstateInput!` - Dados a serem atualizados, incluindo:
  - `name: String` - Nome da imobiliária (opcional)
  - `description: String` - Descrição (opcional)
  - `email: String` - Email (opcional)
  - `phone: String` - Telefone (opcional)
  - `website: String` - Website (opcional)
  - `creci: String` - CRECI (opcional)
  - `active: Boolean` - Status (opcional)
  - `address: RealEstateAddressInput` - Dados de endereço (opcional)
  - `tenant_id: ID` - ID do tenant (opcional, apenas para super-admin)

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
