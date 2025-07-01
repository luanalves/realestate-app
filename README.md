# RealEstate App

Sistema de gestão imobiliária desenvolvido com foco em performance, escalabilidade e modularidade.
O projeto segue princípios de arquitetura limpa, DDD e modularização baseada em domínio em uma **arquitetura headless e stateless**.

---

## 🧱 Características da Arquitetura

### 🎯 Arquitetura Headless & Stateless

- 🚫 **Sem interface web server-side** - API exclusivamente para consumo por clientes externos
- 🔐 **Autenticação stateless** - JWT tokens via Laravel Passport (sem sessões no servidor)
- 📱 **Frontend agnóstico** - Suporte nativo a SPA, mobile apps, desktop, serverless
- ⚡ **Escalabilidade horizontal** - Sem estado compartilhado entre instâncias

### 📚 Documentação Técnica

- **👤 UserManagement Module**: Complete documentation at [`modules/UserManagement/doc/README.md`](modules/UserManagement/doc/README.md)
  - **🔍 GraphQL API**: [`modules/UserManagement/doc/GraphQL_API.md`](modules/UserManagement/doc/GraphQL_API.md)
  - **⚡ CLI Commands**: [`modules/UserManagement/doc/CLI_Commands.md`](modules/UserManagement/doc/CLI_Commands.md)
- **🏢 Organization Module**: Full API documentation at [`modules/Organization/doc/GraphQL_API.md`](modules/Organization/doc/GraphQL_API.md)
- **🏗️ ADRs**: Architectural decisions at [`doc/architectural-decision-records/`](doc/architectural-decision-records/)
- **📖 Guides**: Development patterns and conventions at [`doc/`](doc/)
- **🌐 GraphQL**: Individual module schemas at `modules/*/GraphQL/`ss & Stateless
- 🚫 **Sem interface web server-side** - API exclusivamente para consumo por clientes externos
- 🔐 **Autenticação stateless** - JWT tokens via Laravel Passport (sem sessões no servidor)
- 📱 **Frontend agnóstico** - Suporte nativo a SPA, mobile apps, desktop, serverless
- ⚡ **Escalabilidade horizontal** - Sem estado compartilhado entre instâncias

### 🛠️ Stack Tecnológica
- �🔧 **Laravel 12** como framework principal
- 🧩 **Arquitetura modular por domínio** (UserManagement, RealEstate, Leads, etc)
- 📡 **GraphQL com Lighthouse** para APIs flexíveis
- 🗄️ **PostgreSQL, Redis, MongoDB** como suporte a diferentes tipos de persistência
- ✉️ **Mensageria (Kafka ou RabbitMQ)** em planejamento futuro

### 📁 Estrutura Modular
Cada módulo possui seus próprios:
  - Controllers
  - Models
  - Providers
  - Migrations
  - Seeders
  - GraphQL Schemas

### 📏 Padrões PSR Implementados
  - PSR-1: Basic Coding Standard
  - PSR-4: Autoloader
  - PSR-7: HTTP Message Interface
  - PSR-11: Container Interface
  - PSR-12: Extended Coding Style Guide
  - PSR-14: Event Dispatcher

---

**Aplicação backend headless e stateless** construída com Laravel 12 utilizando arquitetura modular, GraphQL e suporte a múltiplos bancos (PostgreSQL, MongoDB e Redis).

---

## 📁 Estrutura do Projeto

```
realestate-app/
├── app/
├── bootstrap/
├── config/
├── database/
├── modules/
│   └── UserManagement/
│       ├── Http/
│       ├── Models/
│       ├── Providers/
│       ├── GraphQL/
│       └── Database/
│           ├── Migrations/
│           └── Seeders/
├── routes/
├── public/
└── ...
```

---

## 📦 Módulos

Os módulos seguem uma estrutura isolada com seus próprios:

- Controllers
- Models
- Providers
- GraphQL Schemas
- Migrations & Seeders
- **Padrões Arquiteturais Específicos** (Factory, Strategy, Service Layer, etc.)

### 🏗️ Padrões Arquiteturais Implementados

#### 🏭 Factory Pattern
Utilizado para criação dinâmica de objetos baseado em configurações do ambiente:
- **Exemplo**: `UserRepositoryFactory` detecta automaticamente se cache está disponível
- **Benefícios**: Flexibilidade, facilita testes, configuração automática

#### 🎯 Strategy Pattern  
Implementação de diferentes algoritmos/comportamentos para a mesma interface:
- **Exemplo**: `CachedUserRepository` vs `DatabaseUserRepository`
- **Benefícios**: Performance otimizada, fallback automático, código limpo

#### 🔧 Service Layer Pattern
Camada de aplicação que orquestra operações complexas:
- **Exemplo**: `UserService` gerencia operações de usuário e cache
- **Benefícios**: Separação de responsabilidades, reutilização, testabilidade

#### ⚡ Command Pattern
Comandos Artisan específicos para operações de sistema:
- **Exemplo**: `user:cache`, `user:token-analysis`
- **Benefícios**: Automação, manutenção, monitoramento

### 📁 Estrutura Recomendada por Módulo

```
ModuleName/
├── Console/Commands/       # Comandos Artisan específicos
├── Contracts/              # Interfaces e contratos
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── Factories/              # Padrão Factory
├── GraphQL/
├── Http/
├── Models/
├── Providers/
├── Repositories/           # Implementações Strategy
├── Services/               # Service Layer
└── Tests/                  # Testes abrangentes
```

---

## ⚙️ Configurações obrigatórias

### ✅ Providers

Cada módulo deve registrar seu ServiceProvider no arquivo:

```php
// bootstrap/providers.php

return [
    ...
    Modules\UserManagement\Providers\UserManagementServiceProvider::class,
];
```

### ✅ Migrations

Para que o Laravel reconheça as migrations de dentro dos módulos, você deve registrá-las em:

```php
// app/Providers/AppServiceProvider.php

public function boot(): void
{
    $this->loadMigrationsFrom(base_path('modules/UserManagement/Database/Migrations'));
}
```


## 🧬 Seeders por módulo

Cada módulo possui seu próprio seeder principal localizado em:

```
modules/NomeDoModulo/Database/Seeders/DatabaseSeeder.php
```

Para ativar os seeders do módulo, registre-o no `DatabaseSeeder` principal da aplicação localizado em `database/seeders/DatabaseSeeder.php`:

```php
$this->call(\Modules\UserManagement\Database\Seeders\DatabaseSeeder::class);
```

Isso garante que, ao executar:

```bash
php artisan db:seed
```

Todos os dados daquele módulo sejam populados corretamente.

### ✅ GraphQL (Lighthouse)

- O Lighthouse é usado para expor a API via GraphQL
- Configuração do caminho do schema está em `config/lighthouse.php`:

```php
'schema_path' => base_path('modules/UserManagement/GraphQL/schema.graphql'),
```

- A rota `/graphql` é utilizada para envio das queries e mutations

---

## 🧪 Testes e Uso

- Para testar a mutation de criação de usuários, utilize Postman, Insomnia ou Altair
- Endpoint: `http://realestate.localhost/graphql`

## 🚧 Em desenvolvimento

- Implementação de autenticação
- Integração com mais módulos (Imóveis, Leads, Contratos, etc.)
## ✅ Checklist para criação de um novo módulo

### 📋 Estrutura Básica
- [ ] Criar diretório `modules/NomeModulo`
- [ ] Criar Provider e registrar em `bootstrap/providers.php`
- [ ] Criar migrations e registrar via `AppServiceProvider`
- [ ] Criar GraphQL Schema em `modules/NomeModulo/GraphQL/schema.graphql`
- [ ] Atualizar `config/lighthouse.php` com o caminho do schema, se necessário

### 🏗️ Padrões Arquiteturais (Conforme Necessário)
- [ ] Implementar **Factory Pattern** se houver múltiplas implementações
- [ ] Usar **Strategy Pattern** para algoritmos alternativos (ex: cache vs database)
- [ ] Criar **Service Layer** para lógica de negócio complexa
- [ ] Implementar **Commands** para operações de sistema e manutenção
- [ ] Definir **Interfaces/Contracts** para desacoplamento

### 🧪 Qualidade e Testes
- [ ] Criar testes unitários abrangentes (estruturais e funcionais)
- [ ] Criar testes de integração se necessário
- [ ] Verificar cobertura de testes com PHPUnit
- [ ] Seguir padrões de naming e documentação

### 📚 Documentação
- [ ] Criar diretório `modules/NomeModulo/doc/`
- [ ] Criar `doc/README.md` com visão geral e propósito do módulo
- [ ] Criar `doc/GraphQL_API.md` com documentação completa da API
- [ ] Criar `doc/CLI_Commands.md` se o módulo tiver comandos de terminal
- [ ] Criar Controller/Resolver e Request (FormRequest) para validações
- [ ] Criar Seeders se houver dados base (ex: perfis, categorias, etc)
- [ ] Atualizar README.md principal com a descrição do novo módulo
- [ ] Documentar padrões específicos implementados no módulo

### 📖 Exemplo de Referência
Consulte o módulo `UserManagement` como exemplo completo de implementação incluindo:
- ✅ Factory Pattern com `UserRepositoryFactory`
- ✅ Strategy Pattern com repositórios de cache
- ✅ Service Layer com `UserService`  
- ✅ Commands com `UserCacheCommand`, `TokenAnalysisCommand`, `ResetPasswordCommand`
- ✅ Documentação completa em `modules/UserManagement/doc/`
- ✅ 62 testes unitários (173 assertions)

---

- [ ] Atualizar README.md com a descrição do novo módulo

---

## 🚀 Processo de Soft Launch

### 🔐 Chaves de Segurança OAuth

As chaves OAuth do Laravel Passport (`oauth-private.key` e `oauth-public.key`) devem ser tratadas com segurança:

- **NÃO comite estes arquivos no controle de versão**
- Adicione-os ao `.gitignore`:
  ```
  /storage/oauth-*.key
  ```
- Gere as chaves durante a implantação:
  ```bash
  cd ../realestate-infra && docker compose exec app php artisan passport:keys
  ```
- Configure permissões adequadas em produção:
  ```bash
  cd ../realestate-infra && docker compose exec app chmod 600 storage/oauth-private.key
  ```
- Durante o pipeline de CI/CD, garanta que as chaves sejam geradas como parte do processo de implantação
- Para desenvolvimento local, execute o comando de geração de chaves após a configuração inicial

Estas chaves são componentes críticos de segurança que assinam e verificam tokens de autenticação para sua API. Chaves comprometidas podem permitir acesso não autorizado à API.

---

## 📋 Padrões de Desenvolvimento

### 🏷️ Conventional Commits

Este projeto utiliza o padrão **Conventional Commits** para todas as mensagens de commit. Este padrão facilita a geração automática de CHANGELOGs, versionamento semântico e melhora a legibilidade do histórico.

#### Estrutura Básica
```
<type>(<scope>): <subject>
```

#### Exemplos
```bash
feat(user): add cache layer with Redis support
fix(auth): resolve OAuth2 token expiration issue
refactor(database): extract repository pattern
config(cache): set Redis as default cache driver
docs(readme): update installation instructions
test(user): add unit tests for UserService
```

#### Types Principais
- `feat`: Nova funcionalidade
- `fix`: Correção de bugs
- `refactor`: Refatoração sem mudança de funcionalidade
- `config`: Alterações de configuração
- `docs`: Documentação
- `test`: Testes
- `chore`: Manutenção e dependências

#### Scopes do Projeto
- `auth`: Autenticação e autorização
- `user`: Gestão de usuários
- `cache`: Sistema de cache
- `database`: Configurações de banco
- `config`: Arquivos de configuração
- `graphql`: Schema e resolvers
- `oauth`: Configurações OAuth/Passport

Para guia completo, consulte: [`doc/conventional-commits-guide.md`](doc/conventional-commits-guide.md)

### 📚 Documentação Técnica

- **👤 UserManagement Module**: Complete documentation at [`modules/UserManagement/doc/README.md`](modules/UserManagement/doc/README.md)
  - **🔍 GraphQL API**: [`modules/UserManagement/doc/GraphQL_API.md`](modules/UserManagement/doc/GraphQL_API.md)
  - **⚡ CLI Commands**: [`modules/UserManagement/doc/CLI_Commands.md`](modules/UserManagement/doc/CLI_Commands.md)
- **🏢 Organization Module**: Full API documentation at [`modules/Organization/doc/GraphQL_API.md`](modules/Organization/doc/GraphQL_API.md)
- **🏗️ ADRs**: Architectural decisions at [`doc/architectural-decision-records/`](doc/architectural-decision-records/)
- **📖 Guides**: Development patterns and conventions at [`doc/`](doc/)
- **🌐 GraphQL**: Individual module schemas at `modules/*/GraphQL/`

**Quick Start**: For API usage examples, check the UserManagement module's comprehensive documentation which includes authentication setup, complete request examples, and CLI tools.

---