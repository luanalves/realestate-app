# RealEstate Module

Sistema completo de gestão de imobiliárias para a aplicação RealEstate, implementado com arquitetura modular e relacionamento com Organization.

---

## 📋 Visão Geral

O módulo RealEstate é responsável por todas as operações relacionadas a imobiliárias, incluindo:

- 🏢 **Gestão de imobiliárias** (CRUD completo)
- 🔗 **Relacionamento com Organization** (extensão do módulo Organization)
- 📋 **Informações específicas do setor imobiliário** (CRECI, inscrição estadual)
- 🏠 **Endereços de imobiliárias** (sede e filiais)
- 🔒 **Segurança e autorização** baseada em roles

---

## 🎯 Arquitetura & Características

### **Modelo de Dados**
- ✅ **Tabela real_estates** - Armazena dados específicos de imobiliárias
- ✅ **Relacionamento 1:1** - Cada RealEstate está vinculada a uma Organization
- ✅ **Cascata na exclusão** - Exclusão em cascata via foreign key
- ✅ **Timestamps** - Controle automático de created_at e updated_at

### **Padrões Arquiteturais Implementados**
- 🏭 **Service Layer** - `RealEstateService` para centralizar a lógica de negócio
- 🔍 **Repository Pattern** - Separação de responsabilidades para acesso a dados
- 📡 **GraphQL API** - Interface completa para operações CRUD via GraphQL

---

## 📊 Modelo de Dados

### **Tabela `real_estates`**

| Coluna             | Tipo               | Descrição                               |
|--------------------|--------------------|----------------------------------------|
| id                 | bigint unsigned    | Chave primária                          |
| organization_id    | bigint unsigned    | Chave estrangeira para organizations    |
| creci              | string             | Número do CRECI da imobiliária          |
| state_registration | string             | Inscrição estadual                      |
| created_at         | timestamp          | Data de criação                         |
| updated_at         | timestamp          | Data de última atualização              |

### **Relacionamentos**
- **Organization (1:1)**: Uma imobiliária está vinculada a exatamente uma organização
- **Addresses (1:N)**: Uma imobiliária pode ter múltiplos endereços

---

## 🛠️ Serviços Principais

### **RealEstateService**
Serviço central que gerencia todas as operações relacionadas a imobiliárias:

- `createRealEstate()`: Cria uma nova imobiliária com sua organização
- `updateRealEstate()`: Atualiza dados de uma imobiliária existente
- `deleteRealEstate()`: Remove uma imobiliária e sua organização
- `getRealEstateById()`: Recupera uma imobiliária pelo ID

---

## 🔐 Autorização

O acesso às operações é controlado pelo sistema de roles:

- **ROLE_SUPER_ADMIN**: Acesso total a todas as imobiliárias
- **ROLE_REAL_ESTATE_ADMIN**: Acesso total às imobiliárias que administra
- **ROLE_REAL_ESTATE_AGENT**: Acesso limitado às imobiliárias vinculadas
- **ROLE_CLIENT**: Acesso somente leitura a dados públicos

---

## 📦 Estrutura do Módulo

```
RealEstate/
├── Database/
│   ├── Migrations/
│   │   └── 2025_06_23_222826_create_real_estates_table.php
│   └── Seeders/
│       ├── DatabaseSeeder.php
│       └── RealEstateSeeder.php
├── GraphQL/
│   ├── Mutations/
│   │   ├── CreateRealEstateResolver.php
│   │   ├── UpdateRealEstateResolver.php
│   │   └── DeleteRealEstateResolver.php
│   ├── Queries/
│   │   └── RealEstateResolver.php
│   └── schema.graphql
├── Models/
│   └── RealEstate.php
├── Providers/
│   └── RealEstateServiceProvider.php
├── Services/
│   └── RealEstateService.php
├── Support/
│   └── RealEstateConstants.php
└── doc/
    ├── README.md
    └── GraphQL_API.md
```

---

## 🔄 Fluxo de Trabalho

1. **Criação**: Uma imobiliária é criada junto com uma organização base
2. **Atualização**: Alterações em dados específicos ou organizacionais
3. **Consulta**: Busca de imobiliárias com filtros e paginação
4. **Exclusão**: Remoção de uma imobiliária (cascata para endereços e organização)

---

## 📚 Documentação Adicional

- **API GraphQL**: [GraphQL_API.md](GraphQL_API.md) - Documentação completa da API GraphQL
- **Módulo Organization**: `../Organization/doc/README.md` - Documentação do módulo base

---

## 🧪 Testes

O módulo inclui testes abrangentes para todas as funcionalidades:
- Testes de queries GraphQL
- Testes de mutations GraphQL
- Testes de autorização
- Testes de validação

Para executar os testes:
```bash
cd ../realestate-infra && docker compose exec app php artisan test --filter=RealEstateGraphQLTest
```
