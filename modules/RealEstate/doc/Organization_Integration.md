# Integração com Sistema de Extensão do Organization

## Visão Geral

O módulo RealEstate integra-se com o sistema de extensão do módulo Organization, permitindo que dados específicos de imobiliárias sejam acessados através de consultas à organizações.

## Como Funciona

### 1. Listener de Injeção de Dados

O módulo RealEstate implementa o listener `InjectRealEstateDataListener` que escuta o evento `OrganizationDataRequested` disparado pelo módulo Organization.

### 2. Dados Injetados

Quando uma organização é consultada via GraphQL e possui uma imobiliária associada, os seguintes dados são injetados:

- `id`: ID da imobiliária
- `creci`: Número de registro do CRECI
- `state_registration`: Inscrição estadual
- `created_at`: Data de criação do registro
- `updated_at`: Data da última atualização

### 3. Estrutura de Arquivos

```
modules/RealEstate/
├── Listeners/
│   └── InjectRealEstateDataListener.php   # Listener que injeta dados
└── Providers/
    └── RealEstateServiceProvider.php      # Registro do listener
```

## Implementação

### Listener

```php
<?php

namespace Modules\RealEstate\Listeners;

use Modules\Organization\Events\OrganizationDataRequested;
use Modules\RealEstate\Models\RealEstate;

class InjectRealEstateDataListener
{
    public function handle(OrganizationDataRequested $event): void
    {
        $organization = $event->organization;
        
        // Verifica se existe um RealEstate associado a esta organização
        $realEstate = RealEstate::where('organization_id', $organization->id)->first();
        
        if ($realEstate) {
            $event->addExtensionData('realEstate', [
                'id' => $realEstate->id,
                'creci' => $realEstate->creci,
                'state_registration' => $realEstate->state_registration,
                'created_at' => $realEstate->created_at,
                'updated_at' => $realEstate->updated_at,
            ]);
        }
    }
}
```

### Registro no Service Provider

```php
<?php

namespace Modules\RealEstate\Providers;

use Illuminate\Support\ServiceProvider;

class RealEstateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ... outros registros
        $this->registerEventListeners();
    }

    protected function registerEventListeners(): void
    {
        $this->app['events']->listen(
            \Modules\Organization\Events\OrganizationDataRequested::class,
            \Modules\RealEstate\Listeners\InjectRealEstateDataListener::class
        );
    }
}
```

## Uso via GraphQL

### Consulta

```graphql
query GetOrganizationWithRealEstate($id: ID!) {
  organization(id: $id) {
    id
    name
    fantasy_name
    cnpj
    description
    email
    phone
    website
    active
    addresses {
      id
      street
      number
      city
      state
      zip_code
      country
    }
    extensionData  # Contém dados da imobiliária
  }
}
```

### Variables

```json
{
  "id": "4"
}
```

### cURL Example

```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "query": "query GetOrganizationWithRealEstate($id: ID!) { organization(id: $id) { id name fantasy_name cnpj description email phone website active addresses { id street number city state zip_code country } extensionData } }",
    "variables": {
      "id": "4"
    }
  }' \
  "http://realestate.localhost/graphql"
```

### Resposta

```json
{
  "data": {
    "organization": {
      "id": "4",
      "name": "Imobiliária ABC",
      "fantasy_name": "ABC Imóveis",
      "cnpj": "98765432109876",
      "description": "Segunda imobiliária para testes",
      "email": "contato@abcimoveis.com.br",
      "phone": "11988888888",
      "website": "https://abcimoveis.com.br",
      "active": true,
      "addresses": [],
      "extensionData": "{\"realEstate\":{\"id\":2,\"creci\":\"J-54321\",\"state_registration\":\"987.654.321.000\",\"created_at\":\"2025-07-04T23:09:15.000000Z\",\"updated_at\":\"2025-07-04T23:09:15.000000Z\"}}"
    }
  }
}
```

## Quando Usar

### Use a Integração com Organization quando:

- Você precisa de dados completos da organização + imobiliária
- Você quer acessar endereços da organização
- Você está construindo interfaces que mostram informações organizacionais
- Você precisa de suporte a múltiplos tipos de organização

### Use Consultas Diretas do RealEstate quando:

- Você precisa apenas de dados específicos da imobiliária
- Você está construindo relatórios focados em dados imobiliários
- Você precisa de paginação de imobiliárias
- Você está fazendo operações em lote em imobiliárias

## Benefícios

1. **Dados Completos**: Obtenha organização + imobiliária em uma consulta
2. **Endereços**: Acesse endereços da organização na mesma consulta
3. **Flexibilidade**: Sistema extensível para outros tipos de organização
4. **Performance**: Carregamento eficiente com relacionamentos adequados

## Estrutura dos Dados de Extensão

O campo `extensionData` retorna uma string JSON que, quando parseada, contém:

```javascript
{
  "realEstate": {
    "id": 2,
    "creci": "J-54321",
    "state_registration": "987.654.321.000",
    "created_at": "2025-07-04T23:09:15.000000Z",
    "updated_at": "2025-07-04T23:09:15.000000Z"
  }
}
```

## Considerações Técnicas

### Performance

- O listener só busca dados se a organização tiver uma imobiliária associada
- Usa relacionamento eficiente do Eloquent
- Não afeta performance de organizações sem imobiliárias

### Compatibilidade

- Funciona independentemente do módulo Organization
- Não quebra se o módulo Organization for atualizado
- Mantem compatibilidade com consultas diretas do RealEstate

### Debugging

Para debuggar problemas com a extensão:

1. Verifique se o listener está registrado no Service Provider
2. Confirme que existe uma imobiliária associada à organização
3. Verifique logs do Laravel para erros no listener
4. Teste consultas diretas do RealEstate para validar dados

## Documentação Relacionada

- [Sistema de Extensão do Organization](../Organization/doc/Extension_System.md)
- [Padrão Observer para Extensões](../../doc/patterns/observer-pattern-for-module-extensions.md)
- [GraphQL API do RealEstate](GraphQL_API.md)
