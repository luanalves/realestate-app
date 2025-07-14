# Padrão Observer para Extensão de Módulos

## Visão Geral

Este documento descreve o padrão Observer implementado para permitir extensões dinâmicas entre módulos no sistema, mantendo o desacoplamento e a flexibilidade.

## Problema Solucionado

O desafio era permitir que módulos especializados (como RealEstate) pudessem estender dados de módulos base (como Organization) sem criar acoplamento direto entre eles.

## Solução Implementada

### 1. Padrão Observer com Eventos Laravel

O sistema utiliza os eventos do Laravel para implementar o padrão Observer:

```
Módulo Base (Organization)
    ↓ (dispara evento)
[OrganizationDataRequested]
    ↓ (escutado por)
Módulo Extensão (RealEstate)
    ↓ (injeta dados)
Resposta Agregada
```

### 2. Estrutura de Arquivos

```
modules/Organization/
├── Events/
│   └── OrganizationDataRequested.php      # Evento base
├── GraphQL/
│   └── Resolvers/
│       └── OrganizationExtensionDataResolver.php  # Resolver que dispara evento
└── doc/
    └── Extension_System.md                 # Documentação do sistema

modules/RealEstate/
├── Listeners/
│   └── InjectRealEstateDataListener.php   # Listener que injeta dados
└── Providers/
    └── RealEstateServiceProvider.php      # Registro do listener
```

### 3. Componentes do Sistema

#### Evento Base
```php
class OrganizationDataRequested
{
    public $organization;
    public $extensionData = [];
    
    public function addExtensionData(string $moduleName, array $data): void
    {
        $this->extensionData[$moduleName] = $data;
    }
}
```

#### Resolver que Dispara o Evento
```php
class OrganizationExtensionDataResolver
{
    public function __invoke(Organization $organization, array $args): array
    {
        $event = new OrganizationDataRequested($organization);
        Event::dispatch($event);
        return $event->getAllExtensionData();
    }
}
```

#### Listener que Injeta Dados
```php
class InjectRealEstateDataListener
{
    public function handle(OrganizationDataRequested $event): void
    {
        $realEstate = RealEstate::where('organization_id', $event->organization->id)->first();
        
        if ($realEstate) {
            $event->addExtensionData('realEstate', [
                'id' => $realEstate->id,
                'creci' => $realEstate->creci,
                // ... outros dados
            ]);
        }
    }
}
```

## Vantagens da Solução

### 1. Desacoplamento Total
- Módulos não conhecem uns aos outros
- Dependências são gerenciadas via eventos
- Fácil adição/remoção de módulos

### 2. Flexibilidade
- Qualquer módulo pode estender qualquer outro
- Múltiplos módulos podem contribuir simultaneamente
- Dados são agregados automaticamente

### 3. Performance
- Apenas dados solicitados são carregados
- Lazy loading via eventos
- Sem consultas desnecessárias

### 4. Manutenibilidade
- Cada módulo mantém sua própria lógica
- Fácil debugging e testes
- Alterações isoladas por módulo

## Implementação Step-by-Step

### 1. Criar o Evento Base

```php
<?php

namespace Modules\BaseModule\Events;

use Illuminate\Foundation\Events\Dispatchable;

class BaseModuleDataRequested
{
    use Dispatchable;
    
    public $baseEntity;
    public $extensionData = [];
    
    public function __construct($baseEntity)
    {
        $this->baseEntity = $baseEntity;
    }
    
    public function addExtensionData(string $moduleName, array $data): void
    {
        $this->extensionData[$moduleName] = $data;
    }
}
```

### 2. Criar o Resolver GraphQL

```php
<?php

namespace Modules\BaseModule\GraphQL\Resolvers;

use Modules\BaseModule\Events\BaseModuleDataRequested;
use Illuminate\Support\Facades\Event;

class BaseModuleExtensionDataResolver
{
    public function __invoke($baseEntity, array $args): array
    {
        $event = new BaseModuleDataRequested($baseEntity);
        Event::dispatch($event);
        return $event->extensionData;
    }
}
```

### 3. Adicionar ao Schema GraphQL

```graphql
type BaseEntity {
    id: ID!
    name: String!
    
    # Campo de extensão
    extensionData: JSON @field(resolver: "Modules\\BaseModule\\GraphQL\\Resolvers\\BaseModuleExtensionDataResolver")
}
```

### 4. Criar o Listener no Módulo Extensão

```php
<?php

namespace Modules\ExtensionModule\Listeners;

use Modules\BaseModule\Events\BaseModuleDataRequested;

class InjectExtensionDataListener
{
    public function handle(BaseModuleDataRequested $event): void
    {
        $extensionData = ExtensionModel::where('base_id', $event->baseEntity->id)->first();
        
        if ($extensionData) {
            $event->addExtensionData('extensionModule', [
                'id' => $extensionData->id,
                'specific_field' => $extensionData->specific_field,
            ]);
        }
    }
}
```

### 5. Registrar o Listener

```php
<?php

namespace Modules\ExtensionModule\Providers;

use Illuminate\Support\ServiceProvider;

class ExtensionModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(
            \Modules\BaseModule\Events\BaseModuleDataRequested::class,
            \Modules\ExtensionModule\Listeners\InjectExtensionDataListener::class
        );
    }
}
```

## Casos de Uso

### 1. Sistemas Multi-Tenant
- Módulos base para entidades gerais
- Módulos específicos para cada tenant
- Dados agregados dinamicamente

### 2. Sistemas de Plugins
- Core fornece eventos base
- Plugins injetam funcionalidades específicas
- Ativação/desativação dinâmica

### 3. Arquitetura Modular
- Módulos independentes
- Integração via eventos
- Escalabilidade horizontal

## Boas Práticas

### 1. Nomeação Consistente
- Use nomes descritivos para eventos
- Mantenha padrão para listeners
- Organize por namespace

### 2. Documentação
- Documente eventos disponíveis
- Especifique formato dos dados
- Mantenha exemplos atualizados

### 3. Performance
- Verifique necessidade antes de buscar dados
- Use lazy loading quando possível
- Evite consultas N+1

### 4. Testes
- Teste eventos isoladamente
- Valide integração entre módulos
- Simule cenários de falha

## Exemplo Completo - RealEstate

O módulo RealEstate implementa este padrão para estender Organization:

1. **Evento**: `OrganizationDataRequested`
2. **Listener**: `InjectRealEstateDataListener`
3. **Registro**: `RealEstateServiceProvider`
4. **Uso**: Query GraphQL retorna dados agregados

**Consulta:**
```graphql
query {
  organization(id: "1") {
    id
    name
    extensionData  # Contém dados do RealEstate
  }
}
```

**Resposta:**
```json
{
  "data": {
    "organization": {
      "id": "1",
      "name": "Imobiliária ABC",
      "extensionData": "{\"realEstate\":{\"id\":1,\"creci\":\"J-12345\"}}"
    }
  }
}
```

## Considerações Técnicas

### 1. Ordem de Execução
- Listeners são executados na ordem de registro
- Dados podem ser sobrescritos por listeners posteriores
- Use nomes únicos para evitar conflitos

### 2. Gerenciamento de Erros
- Listeners não devem interromper outros listeners
- Capture e log erros adequadamente
- Mantenha dados consistentes em caso de falha

### 3. Performance
- Eventos são síncronos por padrão
- Considere eventos assíncronos para operações pesadas
- Monitor performance com múltiplos listeners

### 4. Compatibilidade
- Mantenha compatibilidade com versões anteriores
- Documente mudanças na estrutura dos dados
- Use versionamento para breaking changes

## Recursos Adiccionais

- [Laravel Events Documentation](https://laravel.com/docs/events)
- [GraphQL Lighthouse Documentation](https://lighthouse-php.com/)
- [Architectural Decision Records](../architectural-decision-records/)

## Próximos Passos

1. Implementar sistema de cache para dados de extensão
2. Adicionar suporte a eventos assíncronos
3. Criar sistema de validação para dados de extensão
4. Implementar versionamento de APIs de extensão
