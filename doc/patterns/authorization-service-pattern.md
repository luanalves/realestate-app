# Authorization Service Pattern

## 📋 Visão Geral

O **Authorization Service Pattern** é um padrão de desenvolvimento que centraliza a lógica de autenticação e autorização em classes de serviço dedicadas, eliminando duplicação de código e facilitando a manutenção do sistema.

## 🎯 Objetivos

- **Eliminar duplicação de código** de autenticação/autorização
- **Centralizar regras de acesso** em um local único
- **Facilitar manutenção** e alterações nas permissões
- **Melhorar testabilidade** dos componentes
- **Usar constantes** em vez de strings mágicas para roles

## 🏗️ Estrutura do Padrão

### 1. Classe de Serviço de Autorização

```php
<?php

declare(strict_types=1);

namespace Modules\Security\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class SecurityAuthorizationService
{
    /**
     * Roles that have access to security logs.
     */
    private const AUTHORIZED_ROLES = [
        RolesSeeder::ROLE_SUPER_ADMIN,
        RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
    ];

    /**
     * Check if user is authenticated and authorized to access security logs.
     *
     * @throws AuthenticationException
     */
    public function authorizeSecurityLogAccess(): User
    {
        // Check if user is authenticated
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to access security logs');
        }

        $user = Auth::guard('api')->user();

        // Check if user has permission to view security logs
        if (!$this->hasSecurityLogPermission($user)) {
            throw new AuthenticationException('You do not have permission to access security logs');
        }

        return $user;
    }

    /**
     * Check if user has permission to access security logs.
     */
    public function hasSecurityLogPermission(?User $user): bool
    {
        if (!$user || !$user->role) {
            return false;
        }

        return in_array($user->role->name, self::AUTHORIZED_ROLES, true);
    }

    /**
     * Get list of authorized roles for security logs.
     */
    public function getAuthorizedRoles(): array
    {
        return self::AUTHORIZED_ROLES;
    }

    /**
     * Check if a specific role has access to security logs.
     */
    public function isRoleAuthorized(string $roleName): bool
    {
        return in_array($roleName, self::AUTHORIZED_ROLES, true);
    }
}
```

### 2. Uso nos Resolvers GraphQL

#### ❌ **Antes (Código Duplicado):**

```php
class SecurityLogStats
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Código duplicado em todos os resolvers
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated');
        }

        $user = Auth::guard('api')->user();
        
        if (!in_array($user->role?->name, ['super_admin', 'real_estate_admin'], true)) {
            throw new AuthenticationException('You do not have permission');
        }

        // Lógica específica do resolver...
    }
}
```

#### ✅ **Depois (Usando o Service):**

```php
class SecurityLogStats
{
    private SecurityLogService $securityLogService;
    private SecurityAuthorizationService $authService;

    public function __construct(
        SecurityLogService $securityLogService,
        SecurityAuthorizationService $authService
    ) {
        $this->securityLogService = $securityLogService;
        $this->authService = $authService;
    }

    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Uma única linha para autorização
        $this->authService->authorizeSecurityLogAccess();

        $filters = $args['filter'] ?? [];
        return $this->securityLogService->getStatistics($filters);
    }
}
```

## 🚀 Como Implementar

### Passo 1: Criar o Serviço de Autorização

```bash
# Criar o arquivo do serviço
touch modules/[ModuleName]/Services/[ModuleName]AuthorizationService.php
```

### Passo 2: Definir as Constantes de Roles

```php
private const AUTHORIZED_ROLES = [
    RolesSeeder::ROLE_SUPER_ADMIN,
    RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
    // Adicionar outras roles conforme necessário
];
```

### Passo 3: Implementar Métodos de Autorização

```php
public function authorize[Resource]Access(): User
{
    if (!Auth::guard('api')->check()) {
        throw new AuthenticationException('Authentication required');
    }

    $user = Auth::guard('api')->user();

    if (!$this->hasPermission($user)) {
        throw new AuthenticationException('Insufficient permissions');
    }

    return $user;
}
```

### Passo 4: Injetar o Serviço nos Resolvers

```php
public function __construct([ModuleName]AuthorizationService $authService)
{
    $this->authService = $authService;
}

public function __invoke(...): mixed
{
    $this->authService->authorize[Resource]Access();
    
    // Lógica específica do resolver...
}
```

## 📚 Padrões de Desenvolvimento Aplicados

### 1. **Service Layer Pattern**
- Lógica de negócio isolada em serviços
- Reutilização entre diferentes componentes

### 2. **DRY Principle (Don't Repeat Yourself)**
- Eliminação de código duplicado
- Manutenção centralizada

### 3. **Single Responsibility Principle (SRP)**
- Cada classe tem uma responsabilidade única
- Serviço focado apenas em autorização

### 4. **Constants Pattern**
- Uso de constantes em vez de strings mágicas
- Refactoring seguro e IntelliSense

### 5. **Dependency Injection**
- Injeção de dependências
- Facilita testes unitários

## 🧪 Testabilidade

### Teste do Serviço de Autorização

```php
class SecurityAuthorizationServiceTest extends TestCase
{
    public function testAuthorizeSecurityLogAccessWithValidUser(): void
    {
        $mockUser = Mockery::mock(User::class);
        $mockRole = Mockery::mock();
        $mockRole->name = RolesSeeder::ROLE_SUPER_ADMIN;
        $mockUser->role = $mockRole;

        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($mockUser);

        $service = new SecurityAuthorizationService();
        $result = $service->authorizeSecurityLogAccess();

        $this->assertSame($mockUser, $result);
    }

    public function testAuthorizeSecurityLogAccessThrowsExceptionForUnauthorizedUser(): void
    {
        $this->expectException(AuthenticationException::class);

        $mockUser = Mockery::mock(User::class);
        $mockRole = Mockery::mock();
        $mockRole->name = RolesSeeder::ROLE_CLIENT; // Role não autorizada
        $mockUser->role = $mockRole;

        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($mockUser);

        $service = new SecurityAuthorizationService();
        $service->authorizeSecurityLogAccess();
    }
}
```

## 🔄 Aplicação em Outros Módulos

### UserManagement Module

```php
class UserManagementAuthorizationService
{
    private const AUTHORIZED_ROLES = [
        RolesSeeder::ROLE_SUPER_ADMIN,
        RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
    ];

    public function authorizeUserManagementAccess(): User { /* ... */ }
}
```

### Properties Module

```php
class PropertyAuthorizationService
{
    private const AUTHORIZED_ROLES = [
        RolesSeeder::ROLE_SUPER_ADMIN,
        RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
        RolesSeeder::ROLE_REAL_ESTATE_AGENT, // Agentes podem gerenciar propriedades
    ];

    public function authorizePropertyAccess(): User { /* ... */ }
}
```

## 📊 Benefícios

### ✅ **Vantagens**
- **Manutenibilidade:** Mudanças de regras em um local
- **Consistência:** Mesmo comportamento em todo o sistema
- **Testabilidade:** Fácil de testar isoladamente
- **Segurança:** Centralização reduz riscos de falhas
- **Performance:** Evita validações duplicadas

### ⚠️ **Considerações**
- Criar serviços específicos para cada contexto
- Não misturar lógicas de autorização diferentes
- Manter consistência nos nomes dos métodos
- Documentar as regras de acesso

## 🎯 Próximos Passos

1. **Implementar em outros módulos** seguindo o mesmo padrão
2. **Criar testes unitários** para todos os serviços de autorização
3. **Documentar regras de acesso** para cada recurso
4. **Considerar middleware** para autorização automática
5. **Implementar cache** para otimizar verificações frequentes

## 📝 Convenções de Nomenclatura

- **Classe:** `[ModuleName]AuthorizationService`
- **Método principal:** `authorize[Resource]Access()`
- **Método de verificação:** `has[Resource]Permission()`
- **Constante de roles:** `AUTHORIZED_ROLES`
- **Métodos auxiliares:** `getAuthorizedRoles()`, `isRoleAuthorized()`

---

**Autor:** Luan Silva  
**Data:** 17 de Junho de 2025  
**Versão:** 1.0
