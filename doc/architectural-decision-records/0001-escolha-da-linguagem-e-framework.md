# ADR [0001]: Escolha Da Linguagem E Framework

**Status:** Aceito  
**Data:** 2025-03-24

## Contexto

Durante a definição da arquitetura do sistema imobiliário, foi realizada uma avaliação técnica aprofundada entre as linguagens PHP, Python, Go e Node.js, considerando critérios como:

Segurança e conformidade com boas práticas modernas (CSRF, XSS, autenticação, criptografia, RBAC)

Desempenho e escalabilidade

Suporte ao modelo modular (divisão de domínios e microserviços)

Integração com GraphQL e APIs modernas

Curva de aprendizado e familiaridade da equipe

Facilidade de integração com ferramentas e infraestrutura já utilizadas

Além disso, a aplicação será dividida em múltiplos domínios (módulos) para isolar componentes críticos como: autenticação, API Gateway, importação de arquivos, envio de notificações, processamento assíncrono, e relatórios — com objetivo de identificar gargalos e permitir escalabilidade horizontal.



## Decisão

Optamos por utilizar PHP com o framework Laravel como base do backend da aplicação.

Justificativas principais:
Familiaridade e Produtividade
A equipe possui ampla experiência com PHP e Laravel, o que garante agilidade no desenvolvimento, menor curva de aprendizado e facilidade de manutenção no longo prazo.

Maturidade do Ecossistema
PHP possui uma comunidade sólida, com vasto conjunto de pacotes confiáveis e bem mantidos. Laravel, por sua vez, oferece um ecossistema completo para APIs REST e GraphQL, filas assíncronas, autenticação, cache e muito mais.

Segurança
PHP e Laravel oferecem recursos nativos robustos:

CSRF protection com tokens automáticos

Escapamento de saída padrão (blade templates)

Integração fácil com políticas de RBAC

Suporte nativo à criptografia e proteção contra XSS

Gestão de sessões segura e configurável

Flexibilidade de Arquitetura
Laravel oferece suporte nativo à construção modular e desacoplada via providers, containers de injeção de dependência, middlewares e filas.
Também permite separação clara entre domínios, facilitando a futura transição para microserviços, se necessário.

Suporte a GraphQL
Laravel pode ser facilmente integrado com Laravel Lighthouse, uma biblioteca madura que permite a construção de APIs GraphQL com autenticação, autorização, diretivas customizadas e integração com Eloquent ORM.


## Consequências

A aplicação será construída em PHP com Laravel, utilizando REST e GraphQL (via Laravel Lighthouse) como protocolos principais de comunicação.

A arquitetura seguirá abordagem modular, permitindo que partes críticas da aplicação (como processamento de arquivos, autenticação, fila de mensagens, relatórios) sejam isoladas, testadas e otimizadas individualmente.

Serão utilizados Redis (cache e filas), PostgreSQL (dados relacionais), MongoDB (dados semiestruturados), além de RabbitMQ ou Laravel Horizon para controle de jobs assíncronos.

A escolha por PHP reduz riscos operacionais e acelera o desenvolvimento pela alta aderência da stack ao conhecimento da equipe.