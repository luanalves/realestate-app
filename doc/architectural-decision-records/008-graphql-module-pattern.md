# ADR 008: Padrão de Implementação GraphQL para Módulos

## Contexto

O sistema utiliza o GraphQL como camada de API para todas as operações de CRUD e consultas complexas. Inicialmente, diferentes abordagens foram utilizadas para integrar esquemas GraphQL dos diversos módulos ao esquema principal da aplicação:

1. **Abordagem de importação externa** - Os esquemas dos módulos eram definidos em arquivos separados e importados via diretiva `#import` no esquema principal.

2. **Abordagem integrada** - As definições GraphQL dos módulos eram escritas diretamente no arquivo de esquema principal.

Isto gerou inconsistências no padrão de desenvolvimento, dificultando a manutenção e extensão da aplicação.

## Decisão

**Padronizamos a implementação GraphQL dos módulos através da abordagem modular (importação externa), onde cada módulo mantém seu próprio arquivo schema.graphql que é registrado e importado no esquema principal.**

### Motivos:

1. **Maior Coesão** - Cada módulo é responsável por suas próprias definições GraphQL.
2. **Melhor Encapsulamento** - As definições GraphQL estão contidas no módulo ao qual pertencem.
3. **Facilidade de Manutenção** - Alterações no esquema de um módulo não afetam outros módulos.
4. **Desenvolvimento em Paralelo** - Equipes diferentes podem trabalhar em módulos distintos sem conflitos.
5. **Alinhamento com Arquitetura Modular** - Segue o mesmo princípio de modularização usado em outras partes da aplicação.

## Como Implementar

Para cada novo módulo que requer funcionalidades GraphQL:

1. **Organização do Código**:
   - Crie um arquivo `schema.graphql` no diretório `GraphQL/` do módulo
   - Defina todos os tipos, inputs, queries e mutations do módulo neste arquivo
   - Implemente os resolvers nos diretórios `GraphQL/Queries/` e `GraphQL/Mutations/` dentro do módulo
   - Utilize a camada de serviço para lógica de negócios

2. **Registro do Esquema**:
   - No `ServiceProvider` do módulo, adicione o seguinte código no método `boot()`:

   ```php
   // Registrar o esquema GraphQL do módulo
   config(['lighthouse.schema.register' => array_merge(
       config('lighthouse.schema.register', []),
       [__DIR__ . '/../GraphQL/schema.graphql']
   )]);
   ```

   Ou alternativamente:

   ```php
   // Registrar o esquema GraphQL do módulo
   $this->app->make('config')->set(
       'lighthouse.schema.register', 
       array_merge(
           (array) $this->app->make('config')->get('lighthouse.schema.register', []),
           [__DIR__ . '/../GraphQL/schema.graphql']
       )
   );
   ```

3. **Importação (Opcional)**:
   - Se preferir, além de registrar o esquema via configuração, você também pode adicionar uma importação explícita no esquema principal:
   
   ```graphql
   # No arquivo /graphql/schema.graphql
   #import ../modules/NomeModulo/GraphQL/schema.graphql
   ```

4. **Padrão de Nomenclatura**:
   - Tipos: `ModuloNomeTipo` (ex: `RealEstate`, `RealEstateAddress`)
   - Queries: camelCase, nome descritivo (ex: `realEstateById`, `userProperties`)
   - Mutations: camelCase, verbo + substantivo (ex: `createRealEstate`, `updateProperty`)
   - Inputs: `VerboCamelCaseTipoInput` (ex: `UpdateRealEstateInput`)
   - Use prefixos consistentes para evitar colisões de nomes entre módulos

5. **Extensão de Tipos**:
   - Use `extend type Query` e `extend type Mutation` em vez de redefinir esses tipos
   - Para tipos personalizados, defina-os completamente dentro do arquivo de esquema do módulo

6. **Exemplo de Estrutura de Arquivo**:

   ```graphql
   # No arquivo /modules/ModuloExemplo/GraphQL/schema.graphql
   
   extend type Query {
       exemploConsulta(
           id: ID!
       ): ExemploTipo @field(resolver: "Modules\\ModuloExemplo\\GraphQL\\Queries\\ExemploConsulta")
   }
   
   extend type Mutation {
       exemploMutacao(
           input: ExemploInput!
       ): ExemploTipo! @field(resolver: "Modules\\ModuloExemplo\\GraphQL\\Mutations\\ExemploMutacao")
   }
   
   type ExemploTipo {
       id: ID!
       nome: String!
       # outros campos...
   }
   
   input ExemploInput {
       nome: String!
       # outros campos...
   }
   ```

## Consequências

### Positivas:
- Melhor encapsulamento e coesão do módulo
- Facilidade de manutenção e evolução independente dos módulos
- Melhor organização do código - cada esquema está no mesmo diretório que seus resolvers
- Facilita o desenvolvimento em paralelo por várias equipes
- Arquivos menores e mais fáceis de gerenciar

### Negativas:
- Necessidade de mecanismo para registrar e importar os esquemas dos módulos
- Potencial para duplicação de tipos entre módulos se não houver coordenação adequada
- Necessidade de gerenciar dependências entre os esquemas de diferentes módulos

## Status

Aprovado e implementado em junho de 2025.

## Referências

- [Lighthouse PHP Documentation](https://lighthouse-php.com/master/digging-deeper/schema-organisation.html)
- [GraphQL Schema Stitching](https://www.graphql-tools.com/docs/schema-stitching)
- [Laravel Module Development](https://laravel.com)
- [GraphQL Best Practices](https://graphql.org/learn/best-practices/)
