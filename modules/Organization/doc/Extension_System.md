# Sistema de Extensão de Dados da Organização

## Visão Geral

O módulo Organization implementa um sistema de extensão baseado no padrão Observer que permite outros módulos injetarem dados dinamicamente nas consultas GraphQL de organizações, mantendo o desacoplamento entre módulos.

## Como Funciona

### 1. Evento de Solicitação de Dados

Quando uma consulta GraphQL solicita dados de uma organização com o campo `extensionData`, o resolver dispara o evento `OrganizationDataRequested`.

### 2. Listeners Respondem

Outros módulos podem registrar listeners para este evento e injetar seus próprios dados específicos.

### 3. Dados Agregados

Os dados de todos os listeners são agregados e retornados como um objeto JSON no campo `extensionData`.

## Estrutura do Sistema

```
modules/Organization/
├── Events/
│   └── OrganizationDataRequested.php     # Evento disparado quando dados são solicitados
├── GraphQL/
│   └── Resolvers/
│       └── OrganizationExtensionDataResolver.php  # Resolver que dispara o evento
└── GraphQL/
    └── schema.graphql                     # Schema com campo extensionData
```

## Exemplo de Uso

### Consultando Dados de Extensão

```graphql
query GetOrganization($id: ID!) {
    organization(id: $id) {
        id
        name
        description
        extensionData  # Dados injetados por outros módulos
    }
}
```

### Resposta Esperada

```json
{
    "data": {
        "organization": {
            "id": "1",
            "name": "Organization Name",
            "description": "Organization description",
            "extensionData": {
                "moduleA": {
                    "id": "1",
                    "specific_field": "value",
                    "created_at": "2025-01-01T00:00:00Z",
                    "updated_at": "2025-01-01T00:00:00Z"
                },
                "moduleB": {
                    "id": "2",
                    "another_field": "another_value"
                }
            }
        }
    }
}
```

## Implementando um Listener

### 1. Criar o Listener

```php
<?php

namespace Modules\YourModule\Listeners;

use Modules\Organization\Events\OrganizationDataRequested;

class InjectYourModuleDataListener
{
    public function handle(OrganizationDataRequested $event): void
    {
        $organization = $event->organization;
        
        // Verificar se esta organização tem dados do seu módulo
        $yourModuleData = YourModel::where('organization_id', $organization->id)->first();
        
        if ($yourModuleData) {
            $event->addExtensionData('yourModule', [
                'id' => $yourModuleData->id,
                'specific_field' => $yourModuleData->specific_field,
                // ... outros campos
            ]);
        }
    }
}
```

### 2. Registrar o Listener

No Service Provider do seu módulo:

```php
protected function registerEventListeners(): void
{
    $this->app['events']->listen(
        \Modules\Organization\Events\OrganizationDataRequested::class,
        \Modules\YourModule\Listeners\InjectYourModuleDataListener::class
    );
}
```

### 3. Chamar o Método no Boot

```php
public function boot(): void
{
    // ... outros registros
    $this->registerEventListeners();
}
```

## Vantagens

1. **Desacoplamento**: Os módulos não precisam conhecer uns aos outros
2. **Flexibilidade**: Qualquer módulo pode injetar dados
3. **Performance**: Apenas os dados solicitados são carregados
4. **Extensibilidade**: Fácil adicionar novos tipos de dados

## Boas Práticas

1. **Nomeação**: Use nomes descritivos para os módulos nos dados de extensão
2. **Performance**: Apenas busque dados se necessário (verificar se a organização tem dados do seu módulo)
3. **Estrutura**: Mantenha a estrutura dos dados consistente
4. **Documentação**: Documente os campos que seu módulo injeta

## Testando

Para testar se seu listener está funcionando:

```bash
# Execute uma consulta GraphQL
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "query": "query { organization(id: \"1\") { id name extensionData } }"
  }' \
  "http://your-domain.localhost/graphql"
```

O campo `extensionData` deve conter os dados injetados pelo seu módulo.
