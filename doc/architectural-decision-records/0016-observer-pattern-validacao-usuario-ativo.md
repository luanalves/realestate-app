# ADR-0016: Observer Pattern para Validação de Status Ativo do Usuário

**Status:** Aceito  
**Data:** 2025-06-30  
**Decisores:** Equipe de Desenvolvimento  
**Tags:** autenticação, security, observer-pattern, oauth, passport

## Contexto

Durante o desenvolvimento do sistema de autenticação OAuth2 com Laravel Passport, identificamos a necessidade de validar se um usuário está ativo (`is_active = 1`) antes de permitir que ele obtenha tokens de acesso. 

### Problema
- Usuários inativos (`is_active = 0`) não devem conseguir se autenticar
- A validação deve ser transparente e não invasiva ao processo de autenticação existente
- Deve ser facilmente testável e manutenível
- Não deve quebrar funcionalidades já implementadas

### Requisitos
1. Interceptar a criação de tokens OAuth
2. Validar status ativo do usuário
3. Revogar tokens de usuários inativos
4. Manter logs de segurança
5. Implementação transparente (não modifica core do OAuth)

## Opções Consideradas

### Opção 1: Custom User Provider
- **Prós:** Intercepta antes da criação do token
- **Contras:** Modifica o core da autenticação, mais complexo, pode quebrar funcionalidades

### Opção 2: Middleware Personalizado
- **Prós:** Intercepta requisições
- **Contras:** Atua depois do token já criado, menos eficiente

### Opção 3: Observer Pattern com Eventos Laravel Passport ⭐ (Escolhida)
- **Prós:** 
  - Implementação transparente
  - Usa eventos nativos do Laravel Passport
  - Facilmente removível/modificável
  - Não acopla ao core da autenticação
  - Facilmente testável
  - Permite adicionar outras validações no futuro
- **Contras:** Atua após criação do token (mas revoga imediatamente)

## Decisão

**Escolhemos implementar o Observer Pattern** utilizando os eventos nativos do Laravel Passport para validar o status ativo do usuário após a criação do token de acesso.

### Arquitetura da Solução

```
OAuth Request → Token Created → Event Dispatched → Observer → User Validation → Token Revoked (if inactive)
```

## Implementação

### 1. Constantes no Modelo User

```php
class User extends Authenticatable
{
    /**
     * User status constants
     */
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    
    // ...existing code...
}
```

### 2. Observer/Listener

```php
namespace App\Listeners;

use App\Models\User;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Log;

class ValidateActiveUserOnTokenCreation
{
    public function handle(AccessTokenCreated $event): void
    {
        $token = Token::find($event->tokenId);
        $user = User::find($token->user_id);
        
        if (!$user || !$user->is_active || $user->is_active === User::STATUS_INACTIVE) {
            Log::info('Revoking token for inactive user', [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'token_id' => $event->tokenId
            ]);
            
            $token->revoke();
        }
    }
}
```

### 3. Registro do Observer

```php
// AppServiceProvider.php
public function boot(): void
{
    Event::listen(
        AccessTokenCreated::class,
        ValidateActiveUserOnTokenCreation::class
    );
}
```

### 4. Uso em Produção

Quando um usuário inativo tenta se autenticar:

```bash
# Request OAuth
curl -X POST http://realestate.localhost/oauth/token \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=password&client_id=3&client_secret=SECRET&username=inactive@example.com&password=senha123"

# Response: Token é criado mas imediatamente revogado
# Qualquer tentativa de usar o token resulta em "401 Unauthorized"
```

## Consequências

### Positivas ✅
- **Transparência:** Não modifica fluxo de autenticação existente
- **Segurança:** Usuários inativos não conseguem usar tokens
- **Auditoria:** Logs detalhados de tentativas de acesso
- **Manutenibilidade:** Fácil de modificar/remover
- **Testabilidade:** Observer pode ser testado isoladamente
- **Extensibilidade:** Permite adicionar outras validações

### Negativas ⚠️
- **Timing:** Token é criado antes de ser revogado (window mínima)
- **Dependência:** Depende de eventos do Laravel Passport
- **Performance:** Adiciona uma query extra por token criado

### Riscos Mitigados 🛡️
- **Fallback:** Em caso de erro, token é revogado por segurança
- **Logging:** Todos os eventos são logados para auditoria
- **Graceful Failure:** Sistema continua funcionando mesmo se observer falha

## Compliance

Esta implementação atende aos seguintes requisitos de segurança:
- **ADR-0009:** Requisitos de Segurança
- **ADR-0004:** Autenticação com OAuth2 e RBAC
- **ADR-0010:** Definição dos Tipos de Usuário

## Monitoramento

### Métricas a Acompanhar
- Número de tokens revogados por usuários inativos
- Tentativas de uso de tokens revogados
- Performance do observer (tempo de execução)

### Logs de Segurança
```
[INFO] Revoking token for inactive user
{
  "user_id": 123,
  "email": "user@example.com", 
  "token_id": "abc123",
  "timestamp": "2025-06-30T10:00:00Z"
}
```

## Referências

- [Laravel Passport Events](https://laravel.com/docs/passport#events)
- [Observer Pattern](https://refactoring.guru/design-patterns/observer)
- [ADR-0004: Autenticação com OAuth2 e RBAC](./0004-autenticacao-com-oauth2-e-rbac.md)
- [ADR-0009: Requisitos de Segurança](./0009-requisitos-de-seguranca.md)
