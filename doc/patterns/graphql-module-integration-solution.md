# Integração de Schemas GraphQL em Arquitetura Modular

Este documento descreve a abordagem utilizada para integrar schemas GraphQL modulares na nossa aplicação Laravel com Lighthouse PHP.

## Solução Implementada

Após testar várias abordagens, descobrimos que a maneira mais confiável de integrar schemas GraphQL modulares é através do registro dinâmico dos arquivos de schema pelos ServiceProviders dos módulos.

### 1. Schema Principal (graphql/schema.graphql)

O arquivo principal contém apenas definições globais, como scalars e tipos comuns:

```graphql
scalar DateTime @scalar(class: "Nuwave\\Lighthouse\\Schema\\Types\\Scalars\\DateTime")
scalar JSON @scalar(class: "App\\GraphQL\\Scalars\\JSON")

# Tipos comuns
type PaginatorInfo {
    # ...
}
```

### 2. Schemas dos Módulos

Cada módulo tem seu próprio arquivo schema.graphql que define seus próprios tipos Query e Mutation (sem usar extend):

```graphql
# modules/UserManagement/GraphQL/schema.graphql
type Query {
    users: [User!]! @field(resolver: "Modules\\UserManagement\\GraphQL\\Queries\\Users")
    # ...
}

type Mutation {
    createUser(input: CreateUserInput!): User @field(resolver: "...")
    # ...
}

# Tipos específicos do módulo
type User {
    # ...
}
```

### 3. Configuração do Lighthouse

O arquivo `config/lighthouse.php` mantém o array 'register' vazio inicialmente:

```php
'schema' => [
    'register' => [],
],
```

### 4. ServiceProviders dos Módulos

O aspecto mais importante desta solução é que os ServiceProviders dos módulos registram dinamicamente seus schemas:

```php
public function boot(): void
{
    // Load migrations
    $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

    // Register GraphQL schema
    config(['lighthouse.schema.register' => array_merge(
        config('lighthouse.schema.register', []),
        [__DIR__.'/../GraphQL/schema.graphql']
    )]);
}
```

## Como Funciona

1. Cada ServiceProvider adiciona o caminho para seu schema.graphql ao array de configuração 'lighthouse.schema.register'
2. O Lighthouse combina automaticamente todos os schemas registrados
3. Como cada módulo define seus próprios tipos Query e Mutation completos (não como extensões), não há conflitos de combinação

## Verificação da Configuração

Para confirmar que os schemas estão sendo corretamente registrados e combinados:

1. Execute `php artisan lighthouse:print-schema` para ver o schema completo
2. Teste uma consulta GraphQL que acesse campos de diferentes módulos

## Considerações Importantes

1. **Conflitos de Nome**: 
   - Como todos os módulos definem seus próprios tipos Query e Mutation, certifique-se de que os nomes dos campos sejam únicos entre módulos
   - Se dois módulos definirem um campo com o mesmo nome, o último módulo registrado sobrescreverá o anterior

2. **Ordem de Registro**:
   - A ordem em que os ServiceProviders são registrados pode afetar qual definição de campo prevalece em caso de conflito
   - Os ServiceProviders são geralmente registrados na ordem em que aparecem em `config/app.php`

3. **Manutenção**:
   - Esta abordagem permite que novos módulos sejam adicionados sem modificar arquivos centrais
   - Cada módulo é responsável por registrar seu próprio schema
