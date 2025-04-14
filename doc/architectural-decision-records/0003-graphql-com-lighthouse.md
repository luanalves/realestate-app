# ADR [0003]: Graphql Com Lighthouse

**Status:** Aceito  
**Data:** 2025-04-01

## Contexto

Durante a definição da camada de interface da aplicação, foi necessário escolher o protocolo de comunicação entre o frontend desacoplado e o backend (Laravel).

A equipe avaliou duas abordagens principais: API REST tradicional e GraphQL, considerando os seguintes critérios:

Flexibilidade na definição e obtenção de dados

Redução de over-fetching e under-fetching

Performance em ambientes modulares

Facilidade de integração com frontend moderno

Suporte a autenticação, autorização e políticas de acesso por campo

Curva de aprendizado e suporte no ecossistema Laravel

A aplicação será composta por diversos domínios isolados (como usuários, imóveis, contratos, mensagens, alertas), com tendência de crescimento e interdependência entre módulos. Por isso, buscou-se uma solução mais flexível e evolutiva.

## Decisão

Optamos por utilizar GraphQL como principal interface da API da aplicação, implementado através do pacote Laravel Lighthouse.

Justificativas principais:
Flexibilidade na definição dos dados
GraphQL permite que o cliente especifique exatamente os campos que deseja obter, reduzindo o tráfego e evitando o retorno de dados desnecessários — uma limitação comum nas APIs REST em sistemas complexos.

Alto alinhamento com o frontend 
A estrutura baseada em componentes do React se beneficia diretamente da granularidade de requisições do GraphQL. Além disso, ferramentas como Apollo Client oferecem integração robusta e cache inteligente no frontend.

Escalabilidade modular
A arquitetura da aplicação será composta por módulos/domínios separados, e o uso de schemas, resolvers e diretivas customizadas facilita a extensão e manutenção da API à medida que novos domínios forem adicionados.

Integração com Laravel (Eloquent + Auth)
Lighthouse possui suporte nativo a recursos do Laravel, como autenticação, autorização, Eloquent ORM e validação. Isso facilita a implementação de regras de acesso por tipo, campo e operação.

Performance otimizada com schema-first
O modelo schema-first com lazy loading de resolvers permite que apenas o necessário seja processado a cada requisição, especialmente útil em sistemas com múltiplas origens de dados e acesso condicional.


## Consequências

A aplicação utilizará GraphQL como camada de comunicação primária, exposta através do pacote Laravel Lighthouse.

Toda a modelagem de domínio será refletida no schema GraphQL, com uso intensivo de diretivas personalizadas, autorização contextual e resolvers separados por domínio.

O frontend desacoplado consumirá a API utilizando Apollo Client, com suporte a cache automático, polling e atualização reativa.

Boas práticas como padrão Relay, paginação, rate limiting, throttling, monitoramento de queries lentas e tracing por operação serão adotadas como parte do desenvolvimento contínuo.