# TODOs - Real Estate App

## ✅ Concluído
- [x] Terminar consulta do detalhe de logs
- [x] Implementar testes unitários básicos no módulo de security
- [x] **Documentar como validar a autenticação do cliente e validar a role dele**
  - [x] Criado Authorization Service Pattern
  - [x] Documentação completa em `doc/patterns/authorization-service-pattern.md`
  - [x] Implementado SecurityAuthorizationService
  - [x] Implementado UserManagementAuthorizationService
  - [x] Refatorados todos os resolvers GraphQL para usar os serviços
  - [x] Eliminada duplicação de código em 7+ arquivos
  - [x] Substituídas strings mágicas por constantes de roles
  - [x] Criados testes unitários para os serviços de autorização

## 🔥 Prioridade ALTA

### GraphQL Pagination - Padronização
- [x] **Fix RealEstates query pagination**
  - ✅ Changed return format to match GraphQL schema expectation
  - ✅ Added documentation in `doc/patterns/graphql-pagination-pattern.md`
- [ ] **Audit all paginated queries and fix if needed**
  - [x] SecurityLogs query (correctly implemented)
  - [x] RealEstates query (fixed)
  - [ ] Verify all other paginated queries in all modules
  - [ ] Add pagination handling to future query resolvers
- [ ] **Create Lighthouse pagination helper**
  - [ ] Create utility function to transform Laravel paginators to GraphQL format
  - [ ] Add to shared utilities
  - [ ] Update documentation with examples

### Authorization Service Pattern - Expansão
- [x] **Identificar todos os módulos existentes**
  - ✅ Security: Implementado
  - ✅ UserManagement: Implementado
  - ✅ Apenas 2 módulos existem no projeto

- [ ] **Middleware GraphQL para autorização automática**
  - Criar middleware que aplica autorização baseada em anotações
  - Integrar com Lighthouse GraphQL
  - Documentar uso nos schemas GraphQL

- [ ] **Melhorar documentação do padrão**
  - Adicionar exemplos de uso em diferentes contextos
  - Documentar boas práticas de teste
  - Criar guia de implementação para novos módulos

### Módulo Security - Testes GraphQL
- [ ] **Corrigir autenticação nos testes GraphQL**
  - Problema: Mock de autenticação com Passport::actingAs() falha
  - Solução: Usar factory de user real com role adequada (super_admin/real_estate_admin)
  - Arquivo: `tests/Feature/Security/SecurityLogGraphQLTest.php`

- [ ] **Adicionar seeders de teste para dados consistentes**
  - Criar dados de SecurityLog para testes
  - Garantir usuários com roles apropriadas existem
  - Dados MongoDB de exemplo para LogDetail

### Módulo UserManagement - Funcionalidades Essenciais
- [ ] **Gestão de Senha**
  - [ ] Implementar mutation para alteração de senha (changePassword)
  - [ ] Implementar fluxo de recuperação de senha (requestPasswordReset, resetPassword)
  - [ ] Testes para alteração e recuperação de senha
- [ ] **Associação Multi-Tenant (Imobiliárias)**
  - [ ] Garantir campo tenant_id em users
  - [ ] Restringir queries/mutations por tenant_id (exceto Master Admin)
  - [ ] Testes de acesso multi-tenant
- [ ] **Dados de Perfil**
  - [ ] Query para visualização de perfil (me)
  - [ ] Mutation para edição de perfil (updateProfile)
  - [ ] Mutation para upload de avatar (uploadAvatar)
  - [ ] Mutation para preferências pessoais (updatePreferences)
  - [ ] Testes de perfil e preferências
- [ ] **Listagem e Gerenciamento de Usuários (Backoffice)**
  - [ ] Query para listar usuários por imobiliária (usersByTenant)
  - [ ] Mutation para ativar/inativar usuário (setUserActiveStatus)
  - [ ] Mutation para resetar senha de usuário (adminResetUserPassword)
  - [ ] Testes de gerenciamento de usuários

### Módulo BFFAuth - Backend-for-Frontend (Full)
- [ ] **Implementar módulo BFFAuth para autenticação centralizada e proxy de requisições**
  - [ ] Criar estrutura de diretórios: Controllers, Requests, Services, Providers, routes, Tests/Feature
  - [ ] Implementar endpoints:
    - [ ] POST /bffauth/login (login e geração de token)
    - [ ] POST /bffauth/refresh (refresh de token)
    - [ ] POST /bffauth/logout (revogação de token)
    - [ ] POST /bffauth/graphql (proxy para requisições GraphQL autenticadas)
  - [ ] Garantir que o client_secret nunca seja exposto ao frontend
  - [ ] Validar tokens e repassar requisições para o backend principal
  - [ ] Adicionar testes automatizados para todos os endpoints
  - [ ] Documentar o fluxo e a arquitetura no README do módulo
  - [ ] Consultar ADRs para garantir aderência ao padrão do projeto

## 🔶 Prioridade MÉDIA

### Módulo Security - Completar Testes Faltantes
- [ ] **SecurityLogService integration tests**
  - Testes com database real para getStatistics()
  - Testes de filtros complexos
  - Testes de paginação com dados reais

- [ ] **Resolvers GraphQL unitários isolados**
  - SecurityLogQuery resolver individual
  - SecurityLogs resolver com mocks
  - SecurityLogStats resolver isolado
  - SecurityLogDetails resolver com MongoDB mock

- [ ] **Testes de autorização específicos**
  - Verificar roles super_admin e real_estate_admin têm acesso
  - Verificar roles client e real_estate_agent são negados
  - Testes de diferentes cenários de permissão

- [ ] **Testes de validação de entrada**
  - Validação de filtros inválidos
  - Validação de parâmetros de paginação
  - Validação de ordenação com colunas inexistentes



## 📊 Status Atual do Projeto

### Módulo Security
```
✅ Middleware: 100% funcional (10/10 testes)
✅ Models: 100% funcional (8/8 testes) 
✅ Service (partial): 67% funcional (2/3 testes)
✅ Authorization Service: 100% funcional (novo)
❌ GraphQL Resolvers: 0% funcional (0/7 testes)
❌ Integration Tests: 0% funcional (0/7 testes)

TOTAL: 75% dos testes funcionais
```

### Módulo UserManagement
```
✅ Authorization Service: 100% funcional (novo)
✅ Existing Tests: 100% funcional (83/83 testes)
✅ Refactored Resolvers: 100% funcional (5/5 resolvers)

TOTAL: 100% dos testes funcionais
```

### Authorization Service Pattern
```
✅ Security Module: Implementado
✅ UserManagement Module: Implementado
✅ Documentation: Completa (doc/patterns/)
✅ Module Coverage: 100% (2/2 módulos existentes)
❌ Middleware Integration: Pendente

TOTAL: 80% implementado (4/5 tarefas)
```

**Meta:** Atingir 95%+ de cobertura de testes funcionais em todos os módulos

## 🎯 Próximos Marcos

1. **Completar Authorization Service Pattern** (expandir para todos os módulos)
2. **Resolver testes GraphQL** do módulo Security  
3. **Implementar middleware GraphQL** para autorização automática
4. **Documentar outros padrões** identificados no projeto

## Observações Técnicas
- O model `User` deve conter o campo `tenant_id` para associação multi-tenant.
- Todos os acessos (queries e mutations) devem ser protegidos com middleware do tipo `auth` e `can` (autorização baseada em permissões/roles).


--------------------------------------------------------------------------------------------

Domínio: Property (Gestão de Imóveis)
🗂 História: Cadastro de Imóveis
Descrição:
Como um gestor ou corretor de imobiliária, desejo cadastrar imóveis detalhadamente no sistema para disponibilizá-los facilmente para potenciais clientes, promovendo maior visibilidade e eficiência nas negociações.

Critérios de Aceitação:
Cadastro completo com validação dos campos essenciais.

Upload de fotos e vídeos.

Possibilidade de definir status (disponível, alugado, vendido).

Cada imóvel deve ser vinculado claramente à imobiliária responsável.

⚙️ Tarefas Técnicas:
📌 Tarefa: Criar Migration para tabela "properties"
Status: Pending

Priority: High

Feature Type: Migration

Requisitos:

Criar campos principais com base em pesquisa dos principais portais imobiliários (Zap, OLX, QuintoAndar, VivaReal):

Título do imóvel

Descrição detalhada

Tipo do imóvel (Casa, Apartamento, Comercial, Terreno)

Status do imóvel (Disponível, Alugado, Vendido)

Endereço completo (Rua, Número, Bairro, Cidade, Estado, CEP)

Preço (venda/aluguel)

Área total e útil

Quartos, Banheiros, Garagens

Características adicionais (Piscina, Elevador, etc.)

Data de publicação

ID da imobiliária responsável

📌 Tarefa: Criar Model "Property"
Status: Pending

Priority: High

Feature Type: Model

Requisitos:

Relacionar model Property com RealEstate (imobiliária responsável)

Definir casts adequados (ex: preço como decimal, área como float)

📌 Tarefa: Implementar Mutation GraphQL para Cadastro de Imóveis
Status: Pending

Priority: High

Feature Type: GraphQL Mutation

GraphQL Schema:

graphql
Copiar
Editar
extend type Mutation {
    createProperty(input: CreatePropertyInput! @spread): Property! 
      @field(resolver: "Property\\GraphQL\\Mutations\\CreatePropertyMutation") 
      @auth(guard: "api")
}

input CreatePropertyInput {
    title: String! @rules(apply: ["required", "string", "max:255"])
    description: String! @rules(apply: ["required", "string"])
    propertyType: PropertyType! @rules(apply: ["required"])
    status: PropertyStatus! @rules(apply: ["required"])
    price: Float! @rules(apply: ["required", "numeric", "min:0"])
    address: AddressInput! @rules(apply: ["required"])
    features: PropertyFeaturesInput
    realEstateId: ID! @rules(apply: ["required", "exists:real_estates,id"])
}

enum PropertyType {
    APARTMENT
    HOUSE
    COMMERCIAL
    LAND
}

enum PropertyStatus {
    AVAILABLE
    RENTED
    SOLD
}

input AddressInput {
    street: String!
    number: String!
    neighborhood: String!
    city: String!
    state: String!
    zipCode: String!
}

input PropertyFeaturesInput {
    bedrooms: Int
    bathrooms: Int
    area: Float
    hasGarage: Boolean
    hasPool: Boolean
}
📌 Tarefa: Criar Resolver para Mutation GraphQL
Status: Pending

Priority: High

Feature Type: Service/Resolver

Requisitos:

Implementar validação adicional de regras específicas (como limites mínimos e máximos de valores)

Manipular upload de mídias (imagens e vídeos)

Garantir vinculação correta do imóvel à imobiliária autenticada

🗂 História: Upload e Gestão de Mídia do Imóvel
Descrição:
Como corretor ou gestor, desejo fazer upload e gestão de fotos e vídeos dos imóveis diretamente pelo sistema, facilitando a exibição visual atrativa aos clientes.

Critérios de Aceitação:
Upload fácil e rápido de mídias (fotos e vídeos).

Validação automática de formatos aceitos.

Associação automática das mídias ao imóvel correto.

⚙️ Tarefas Técnicas:
📌 Tarefa: Criar Migration para tabela "property_media"
Status: Pending

Priority: Medium

Feature Type: Migration

Requisitos:

Criar tabela com campos:

ID do imóvel (property_id)

Tipo de mídia (imagem ou vídeo)

URL do arquivo armazenado

Flag para mídia principal (destaque)

Timestamp de criação e atualização

📌 Tarefa: Criar Model "PropertyMedia"
Status: Pending

Priority: Medium

Feature Type: Model

Requisitos:

Relacionamento com Model Property

📌 Tarefa: Implementar Mutation GraphQL para Upload de Mídia
Status: Pending

Priority: Medium

Feature Type: GraphQL Mutation

GraphQL Schema:

graphql
Copiar
Editar
extend type Mutation {
    uploadPropertyMedia(input: UploadPropertyMediaInput! @spread): PropertyMedia!
      @field(resolver: "Property\\GraphQL\\Mutations\\UploadPropertyMediaMutation")
      @auth(guard: "api")
}

input UploadPropertyMediaInput {
    propertyId: ID! @rules(apply: ["required", "exists:properties,id"])
    media: Upload! @rules(apply: ["required", "mimes:jpg,jpeg,png,mp4,mov"])
    isPrimary: Boolean = false
}
📌 Tarefa: Implementar serviço de armazenamento e validação de mídia
Status: Pending

Priority: Medium

Feature Type: Service

Requisitos:

Validar tamanho e formato das mídias antes de armazenar

Usar storage do Laravel (AWS S3 ou local no desenvolvimento)

🗂 História: Pesquisa e Listagem de Imóveis (básico backend)
Descrição:
Como cliente ou corretor, quero pesquisar imóveis facilmente através de diversos filtros e visualizar informações detalhadas rapidamente.

Critérios de Aceitação:
Pesquisa com filtros por cidade, bairro, preço, tipo e características.

Paginação e ordenação claras e rápidas.

Informações essenciais retornadas de forma otimizada.

⚙️ Tarefas Técnicas:
📌 Tarefa: Criar Query GraphQL de pesquisa de imóveis
Status: Pending

Priority: High

Feature Type: GraphQL Query

GraphQL Schema já fornecido no arquivo tasks.md anterior.

📌 Tarefa: Implementar Resolver para Query de pesquisa de imóveis
Status: Pending

Priority: High

Feature Type: Resolver

Requisitos:

Filtragem dinâmica e eficiente usando Criteria Pattern ou Query Builder.

Suporte a paginação com Lighthouse.

📚 Pesquisas Necessárias (dev):
Conferir campos adicionais que grandes sites imobiliários usam para melhorar a completude dos cadastros (Zap, QuintoAndar, OLX, VivaReal).

Validação dos formatos e limites das mídias mais usados no mercado imobiliário.

Essas histórias e tarefas estruturadas e detalhadas oferecem clareza suficiente para o desenvolvimento backend inicial com Laravel e GraphQL, e permitem ao time de desenvolvimento atuar de forma clara, objetiva e autônoma.

## 🔰 Novo Módulo: Arquitetura Abstrata para Organizações e Membros

### ✅ Concluído na Implementação Base
- [x] Configuração inicial do módulo RealEstate
- [x] Implementação das migrations para tabelas de imobiliárias
- [x] Implementação dos modelos e relacionamentos básicos
- [x] Configuração do GraphQL para consultas básicas de imobiliárias
- [x] Mutation para criação de imobiliárias
- [x] Implementação de endereços para imobiliárias
- [x] Relação de endereços múltiplos para imobiliárias
- [x] Query GraphQL para buscar imobiliária por ID com endereços

### 🚧 Em Andamento: Implementação de Relacionamento Abstrato entre Organizações e Usuários

#### 1. Modelo de Dados a Implementar

##### 1.1. Migration: Criar tabela pivot `organization_memberships`:

```php
Schema::create('organization_memberships', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->morphs('organization'); // Permite relacionar com qualquer modelo de organização (real_estates, companies, etc.)
    $table->string('role')->nullable(); // Papel do usuário na organização (mais abstrato que cargos específicos)
    $table->string('position')->nullable(); // Cargo/posição na organização
    $table->boolean('is_active')->default(true);
    $table->timestamp('joined_at')->nullable();
    $table->softDeletes();
    $table->timestamps();
    
    // Índices para performance
    $table->index(['organization_type', 'organization_id']);
    $table->unique(['user_id', 'organization_type', 'organization_id'], 'org_membership_unique');
});
```

#### 2. Modelos e Relacionamentos

##### 2.1. Criar um trait `HasOrganizationMemberships` para modelos de organizações:

```php
<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace App\Traits;

trait HasOrganizationMemberships
{
    /**
     * Relação com os membros da organização
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function members()
    {
        return $this->morphToMany(
            \App\Models\User::class,
            'organization',
            'organization_memberships',
            'organization_id',
            'user_id'
        )->withPivot(['role', 'position', 'is_active', 'joined_at'])
         ->withTimestamps();
    }
}
```

##### 2.2. Atualizar o modelo `RealEstate`:

```php
<?php

// ... existing imports ...
use App\Traits\HasOrganizationMemberships;

class RealEstate extends Model
{
    use HasFactory, HasOrganizationMemberships;
    
    // ... existing code ...
}
```

##### 2.3. Atualizar o modelo `User`:

```php
/**
 * Relação com todas as organizações que o usuário é membro
 * 
 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
 */
public function organizations()
{
    return $this->morphedByMany(
        \Modules\RealEstate\Models\RealEstate::class,
        'organization',
        'organization_memberships',
        'user_id',
        'organization_id'
    )->withPivot(['role', 'position', 'is_active', 'joined_at'])
     ->withTimestamps();
}

/**
 * Relação específica com imobiliárias onde o usuário é membro
 * 
 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
 */
public function realEstates()
{
    return $this->organizations()->where('organization_type', \Modules\RealEstate\Models\RealEstate::class);
}
```

#### 3. Configuração de Papéis Abstratos

##### 3.1. Atualizar `RolesSeeder` para ter papéis mais abstratos:

```php
// Em vez de papéis específicos para imobiliária
public const ROLE_ADMIN = 'admin';           // Em vez de real_estate_admin
public const ROLE_AGENT = 'agent';           // Em vez de real_estate_agent
public const ROLE_MEMBER = 'member';         // Papel genérico
public const ROLE_CLIENT = 'client';         // Mantido
public const ROLE_SUPER_ADMIN = 'super_admin'; // Mantido
```

#### 4. GraphQL Schema

##### 4.1. Atualizar o schema GraphQL:

```graphql
interface Organization {
    id: ID!
    name: String!
    members: [OrganizationMembership!]! @morphMany
}

type OrganizationMembership {
    id: ID!
    user: User!
    role: String
    position: String
    isActive: Boolean!
    joinedAt: DateTime
}

# RealEstate agora implementa a interface Organization
type RealEstate implements Organization {
    id: ID!
    name: String!
    # ... outros campos existentes ...
    
    # Implementação da interface Organization
    members: [OrganizationMembership!]! @morphMany
}

extend type User {
    "Organizações onde o usuário é membro"
    organizations: [OrganizationMembership!]! @morphMany
    
    "Imobiliárias onde o usuário é membro (para compatibilidade)"
    realEstates: [RealEstate!]! @field(resolver: "App\\GraphQL\\Queries\\UserRealEstates")
}

extend type Mutation {
    "Adicionar um membro à organização"
    addOrganizationMember(
        organizationType: String!  # "RealEstate", "Company", etc.
        organizationId: ID!
        userId: ID!
        role: String!           # "admin", "agent", "member"
        position: String
        joinedAt: DateTime
    ): Organization! @auth(ability: "manage_organization")
    
    "Remover um membro da organização"
    removeOrganizationMember(
        organizationType: String!
        organizationId: ID!
        userId: ID!
    ): Organization! @auth(ability: "manage_organization")
    
    "Atualizar informações do membro na organização"
    updateOrganizationMember(
        organizationType: String!
        organizationId: ID!
        userId: ID!
        role: String
        position: String
        isActive: Boolean
    ): Organization! @auth(ability: "manage_organization")
}
```

#### 5. Implementação dos Resolvers

##### 5.1. Criar resolver genérico para adicionar membro:

```php
<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AddOrganizationMember
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Resolver o modelo de organização baseado no tipo
        $organizationClass = $this->resolveOrganizationClass($args['organizationType']);
        $organization = $organizationClass::findOrFail($args['organizationId']);
        $user = User::findOrFail($args['userId']);
        
        // Verificar se o usuário já está associado à organização
        if (!$organization->members()->where('user_id', $user->id)->exists()) {
            $pivotData = [
                'role' => $args['role'] ?? 'member',
                'position' => $args['position'] ?? null,
                'joined_at' => $args['joinedAt'] ?? now(),
                'is_active' => true
            ];
            
            $organization->members()->attach($user->id, $pivotData);
        }
        
        return $organization;
    }
    
    /**
     * Resolve o nome completo da classe baseado no tipo de organização
     *
     * @param string $type
     * @return string
     */
    protected function resolveOrganizationClass(string $type): string
    {
        $map = [
            'RealEstate' => \Modules\RealEstate\Models\RealEstate::class,
            // Adicionar outros tipos de organização aqui conforme necessário
        ];
        
        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("Tipo de organização inválido: {$type}");
        }
        
        return $map[$type];
    }
}
```

#### 6. Testes a Implementar

1. Teste de adição de membro a qualquer tipo de organização
2. Teste de remoção de membro
3. Teste de atualização de informações do membro
4. Teste de obtenção de membros por organização
5. Teste de obtenção de organizações por usuário

### 🔮 Tarefas Futuras

- [ ] Implementação de um sistema de permissões dinâmicas baseadas em papel e organização
- [ ] Desenvolvimento de módulos para outros tipos de organizações (além de imobiliárias)
- [ ] Sistema de notificações para membros de organizações
- [ ] Histórico de atividades por membro/organização

--------------------------------------------------------------------------------------------