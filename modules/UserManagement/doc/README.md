# UserManagement Module

Sistema completo de gestão de usuários, autenticação e autorização para a aplicação RealEstate, implementado com arquitetura **headless e stateless**.

---

## 📋 Visão Geral

O módulo UserManagement é responsável por todas as operações relacionadas a usuários, incluindo:

- 🔐 **Autenticação JWT** via Laravel Passport (stateless)
- 👥 **Gestão de usuários** (CRUD completo)
- 🎭 **Sistema de roles** e permissões
- 📱 **Preferências de usuário** (configurações personalizadas)
- 🏢 **Multi-tenancy** via tenant_id
- ⚡ **Cache Redis** para performance otimizada
- 🔒 **Segurança avançada** com validações robustas

---

## 🎯 Arquitetura & Características

### **Headless & Stateless**
- ✅ **Sem sessões no servidor** - Toda autenticação via JWT tokens
- ✅ **Frontend agnóstico** - Suporte a SPA, mobile apps, desktop
- ✅ **Escalabilidade horizontal** - Sem estado compartilhado entre instâncias
- ✅ **API-first** - Exclusivamente GraphQL para todas as operações

### **Padrões Arquiteturais Implementados**
- 🏭 **Factory Pattern** - `UserRepositoryFactory` para criação automática de repositórios
- 🎯 **Strategy Pattern** - `CachedUserRepository` vs `DatabaseUserRepository`
- 🔧 **Service Layer** - `UserService` para lógica de negócio
- ⚡ **Command Pattern** - Comandos CLI para operações administrativas

---

## 🛠️ Stack Tecnológica

- **Framework**: Laravel 12
- **API**: GraphQL com Lighthouse PHP
- **Autenticação**: Laravel Passport (OAuth2 + JWT)
- **Cache**: Redis (strategy pattern com fallback para database)
- **Banco de Dados**: PostgreSQL
- **Logs**: MongoDB (via Security module)
- **Testes**: PHPUnit (62 testes unitários, 173 assertions)

---

## 📁 Estrutura do Módulo

```
UserManagement/
├── Console/Commands/          # Comandos Artisan
│   ├── UserCacheCommand.php   # Gestão de cache
│   ├── TokenAnalysisCommand.php # Análise de tokens OAuth
│   └── ResetPasswordCommand.php # Reset de senhas
├── Contracts/                 # Interfaces
│   └── UserRepositoryInterface.php
├── Database/
│   ├── Factories/             # Factories para testes
│   ├── Migrations/            # Schema de banco
│   └── Seeders/               # Dados iniciais
├── Factories/                 # Factory Pattern
│   └── UserRepositoryFactory.php
├── GraphQL/                   # API GraphQL
│   ├── Mutations/             # Operações de escrita
│   ├── Queries/               # Operações de leitura
│   └── schema.graphql         # Schema principal
├── Http/                      # Controllers web (se necessário)
├── Models/                    # Eloquent models
├── Providers/                 # Service providers
├── Repositories/              # Strategy Pattern
│   ├── CachedUserRepository.php
│   └── DatabaseUserRepository.php
├── Services/                  # Service Layer
│   └── UserService.php
├── Tests/                     # Testes automatizados
└── doc/                       # Documentação
    ├── README.md              # Este arquivo
    ├── GraphQL_API.md         # Documentação completa da API
    └── CLI_Commands.md        # Comandos de terminal
```

---

## 🚀 Funcionalidades Principais

### **👤 Gestão de Usuários**
- ✅ Criação, atualização e exclusão de usuários
- ✅ Validações robustas de email, senha e dados
- ✅ Sistema de roles (super_admin, real_estate_admin, agent, client)
- ✅ Multi-tenancy com tenant_id
- ✅ Preferências JSON flexíveis

### **🔐 Autenticação & Segurança**
- ✅ Login via GraphQL com retorno de JWT token
- ✅ Autenticação stateless (sem sessões)
- ✅ Validação automática de tokens em todas as operações protegidas
- ✅ Reset de senha via email com tokens seguros
- ✅ Alteração de senha autenticada

### **⚡ Performance & Cache**
- ✅ Cache Redis automático para consultas de usuários
- ✅ Fallback gracioso para database se Redis indisponível
- ✅ TTL configurável (15 minutos padrão)
- ✅ Invalidação automática em atualizações/exclusões
- ✅ 95% de redução no tempo de resposta da query `me`

### **🔧 Operações Administrativas**
- ✅ Comandos CLI para gestão de cache
- ✅ Análise detalhada de tokens OAuth
- ✅ Reset de senhas via linha de comando
- ✅ Estatísticas de uso e performance

---

## 📚 Documentação Detalhada

### **🌐 GraphQL API**
Para documentação completa da API GraphQL, incluindo todos os endpoints, exemplos de uso e respostas:

👉 **[GraphQL API Documentation](GraphQL_API.md)**

- Queries: `me`, `users`, `user`
- Mutations: `login`, `createUser`, `updateUser`, `deleteUser`, `changePassword`, `resetPassword`, `updateProfile`, `updatePreferences`
- Exemplos de cURL e respostas
- Tratamento de erros e validações

### **⚡ Comandos CLI**
Para documentação completa dos comandos de terminal disponíveis:

👉 **[CLI Commands Documentation](CLI_Commands.md)**

- `user:cache` - Gestão de cache Redis
- `oauth:tokens` - Análise de tokens OAuth
- `user:reset-password` - Reset de senhas administrativo

---

## 🧪 Qualidade & Testes

### **Cobertura de Testes**
- ✅ **62 testes unitários** com 173 assertions
- ✅ **Testes de integração** para GraphQL
- ✅ **Mocks automáticos** para isolamento de testes
- ✅ **Cobertura de 87%** das funcionalidades principais

### **Executar Testes**
```bash
# Todos os testes do módulo
cd ../realestate-infra && docker compose exec app php artisan test --filter=UserManagement

# Testes específicos
cd ../realestate-infra && docker compose exec app php artisan test --filter=UserGraphQLTest
cd ../realestate-infra && docker compose exec app php artisan test --filter=UserServiceTest
```

---

## ⚙️ Configuração & Instalação

### **1. Provider Registration**
O módulo já está registrado em `bootstrap/providers.php`:

```php
Modules\UserManagement\Providers\UserManagementServiceProvider::class,
```

### **2. Migrations**
```bash
cd ../realestate-infra && docker compose exec app php artisan migrate
```

### **3. Seeders**
```bash
cd ../realestate-infra && docker compose exec app php artisan db:seed
```

### **4. OAuth Setup**
```bash
# Gerar chaves OAuth
cd ../realestate-infra && docker compose exec app php artisan passport:keys

# Criar cliente OAuth
cd ../realestate-infra && docker compose exec app php artisan passport:client --password
```

### **5. Cache Configuration**
O módulo usa Redis automaticamente se disponível, com fallback para database.

---

## 🔧 Uso Rápido

### **Autenticação**
```bash
# Obter token OAuth
curl -X POST http://realestate.localhost/oauth/token \
  -H "Content-Type: application/json" \
  -d '{"grant_type":"password","client_id":"1","client_secret":"SECRET","username":"user@example.com","password":"password123"}'

# Usar token em consultas GraphQL
curl -X POST http://realestate.localhost/graphql \
  -H "Authorization: Bearer TOKEN" \
  -d '{"query":"query { me { id name email } }"}'
```

### **Gestão Administrativa**
```bash
# Reset de senha
cd ../realestate-infra && docker compose exec app php artisan user:reset-password user@example.com --password=newpass123

# Análise de cache
cd ../realestate-infra && docker compose exec app php artisan user:cache info

# Análise de tokens
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens analyze
```

---

## 🏗️ Extensibilidade

### **Adicionar Novos Campos**
1. Criar migration para o campo
2. Atualizar modelo `User`
3. Atualizar schema GraphQL
4. Criar testes

### **Novos Tipos de Usuário**
1. Adicionar role no seeder `RolesSeeder`
2. Atualizar validações se necessário
3. Documentar no GraphQL_API.md

### **Cache Customizado**
1. Implementar `UserRepositoryInterface`
2. Registrar no `UserRepositoryFactory`
3. Configurar via variáveis de ambiente

---

## 🤝 Contribuição

### **Padrões de Código**
- ✅ PSR-12 para formatação
- ✅ `declare(strict_types=1);` obrigatório
- ✅ Header de copyright em todos os arquivos
- ✅ Documentação PHPDoc completa

### **Testes Obrigatórios**
- ✅ Teste unitário para toda nova funcionalidade
- ✅ Teste de integração para GraphQL endpoints
- ✅ Mocks para isolamento de dependências

### **Conventional Commits**
```bash
feat(user): add multi-factor authentication
fix(auth): resolve token expiration edge case
test(user): add unit tests for UserService cache
docs(user): update GraphQL API documentation
```

---

## 📞 Suporte

Para dúvidas sobre o módulo UserManagement:

1. **Documentação**: Consulte [GraphQL_API.md](GraphQL_API.md) e [CLI_Commands.md](CLI_Commands.md)
2. **Testes**: Execute os testes automatizados para verificar funcionamento
3. **Logs**: Monitore logs via Security module para debugging
4. **Cache**: Use comandos CLI para diagnosticar problemas de performance

---

**Módulo desenvolvido seguindo princípios de arquitetura limpa, DDD e padrões enterprise para máxima escalabilidade e manutenibilidade.**
