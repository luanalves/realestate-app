# ADR [0005]: Estrutura De Modulos Com Seed E Migrations Isoladas

**Status:** Aceito  
**Data:** 2025-04-04  
**Última Atualização:** 2025-06-15

## Contexto

Esta ADR complementa a decisão registrada na [ADR 0002 - Arquitetura Modular com Evolução para Microserviços](0002-arquitetura-modular-com-evolucao-para-microservicos.md), onde foi definido que a aplicação seria construída com base em módulos por domínio de negócio.

Com o avanço da implementação, ficou claro que cada módulo não deve apenas conter suas rotas, resolvers e schemas, mas também **ser responsável por sua própria estrutura de banco de dados e dados de inicialização (seeds)**.

A organização tradicional centralizada de migrations e seeders no diretório `database/` da aplicação principal acaba dificultando o isolamento, reuso e escalabilidade dos módulos.

## Decisão

Cada módulo da aplicação (ex: `UserManagement`, `Properties`, `Contracts`, `Leads`) será responsável por:

- Suas próprias **migrations**, localizadas em `modules/Nome/Database/Migrations`
- Seus próprios **seeders**, localizados em `modules/Nome/Database/Seeders`
- Um **DatabaseSeeder** no módulo, que orquestra os seeders locais
- Suas validações, regras de negócio, eventos e exceções, de forma encapsulada
- **Implementação de padrões arquiteturais específicos** conforme necessário do domínio

Além disso, o `DatabaseSeeder.php` principal da aplicação irá apenas **chamar os seeders principais de cada módulo**, mantendo o core da aplicação limpo e agnóstico aos domínios.

### Padrões Arquiteturais por Módulo

#### 1. Padrão Factory para Repositories
Implementado no módulo `UserManagement`, este padrão permite:
- **Detecção automática de cache**: Factory detecta se Redis/cache está disponível
- **Seleção dinâmica de implementação**: Retorna `CachedUserRepository` ou `DatabaseUserRepository`
- **Flexibilidade de configuração**: Permite forçar uso de cache ou banco direto
- **Facilita testes**: Permite mock de diferentes cenários de cache

#### 2. Strategy Pattern para Cache
- **Interface única**: `UserRepositoryInterface` define contrato comum
- **Implementações específicas**: 
  - `CachedUserRepository`: Implementa cache-first com fallback para banco
  - `DatabaseUserRepository`: Acesso direto ao banco, sem cache
- **Métodos específicos do domínio**: `findByEmailWithRole()`, `findByIdWithRole()`
- **Operações de cache**: `invalidateCache()`, `clearAllCache()`

#### 3. Service Layer Pattern
- **Camada de aplicação**: `UserService` orquestra operações complexas
- **Abstração de repositórios**: Utiliza factory para obter implementação adequada
- **Formatação de dados**: Métodos específicos para formatação de resposta da API
- **Debug e monitoramento**: Métodos para informações de debug do sistema

#### 4. Command Pattern para Operações de Sistema
- **Comandos Artisan específicos**: `user:cache`, `user:token-analysis`
- **Operações de manutenção**: Limpeza de cache, análise de tokens
- **Informações de sistema**: Status do cache, configurações atuais

## Consequências

- A aplicação torna-se **altamente modular e independente por domínio**, facilitando a evolução, manutenção e testes unitários de cada componente.
- Torna-se possível reaproveitar módulos em outros projetos com pouca ou nenhuma modificação.
- O controle de dados sensíveis ou específicos fica descentralizado, respeitando os limites de contexto de cada módulo.
- A estrutura facilita a futura extração de domínios para microserviços, já que todo o conhecimento de negócio está isolado.
- **Flexibilidade arquitetural**: Cada módulo pode implementar padrões específicos às suas necessidades
- **Performance otimizada**: Padrões como Factory e Strategy permitem otimizações específicas (cache, fallback)
- **Facilita testes**: Padrões bem definidos simplificam criação de mocks e testes unitários
- **Manutenibilidade**: Separação clara de responsabilidades e implementações

### Estrutura Recomendada por Módulo

```
ModuleName/
├── Console/
│   └── Commands/           # Comandos Artisan específicos do módulo
├── Contracts/              # Interfaces e contratos
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── Factories/              # Padrão Factory para criação de objetos
├── GraphQL/
│   ├── Mutations/
│   ├── Queries/
│   └── schema.graphql
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Providers/
├── Repositories/           # Implementações de repositórios
├── Services/               # Camada de aplicação/serviços
└── Tests/                  # Testes específicos do módulo
```

### Diretrizes de Implementação

1. **Use Interfaces**: Sempre defina contratos claros através de interfaces
2. **Implemente Factories**: Para objetos com múltiplas implementações ou configurações
3. **Strategy Pattern**: Para algoritmos ou comportamentos alternativos
4. **Service Layer**: Para lógica de negócio complexa que envolve múltiplas entidades
5. **Command Pattern**: Para operações de sistema e manutenção
6. **Testes Abrangentes**: Cada padrão deve ter testes unitários específicos

### Exemplo: Módulo UserManagement

O módulo `UserManagement` serve como referência de implementação, incluindo:
- **Factory Pattern**: `UserRepositoryFactory` para seleção de repositório
- **Strategy Pattern**: `CachedUserRepository` vs `DatabaseUserRepository`
- **Service Layer**: `UserService` para orquestração
- **Commands**: `UserCacheCommand`, `TokenAnalysisCommand`
- **Testes Completos**: 62 testes unitários cobrindo todos os padrões

Essa abordagem reforça a visão de que **cada módulo deve ser tratado como um subproduto completo e autocontido**, responsável por sua própria lógica, dados, comportamento e padrões arquiteturais específicos.