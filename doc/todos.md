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