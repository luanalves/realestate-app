# ADR [0001]: Escolha Da Linguagem E Framework

**Status:** Aceito  
**Data:** 2025-03-24

## Contexto

Durante a definição da arquitetura do sistema imobiliário, foi realizada uma avaliação técnica aprofundada entre as linguagens PHP, Python, Go e Node.js, considerando critérios como:

- Segurança e conformidade com boas práticas modernas (CSRF, XSS, autenticação, criptografia, RBAC)
- Desempenho e escalabilidade
- Suporte ao modelo modular (divisão de domínios e microserviços)
- Integração com GraphQL e APIs modernas
- Curva de aprendizado e familiaridade da equipe
- Facilidade de integração com ferramentas e infraestrutura já utilizadas

Além disso, a aplicação será dividida em múltiplos domínios (módulos) para isolar componentes críticos como: autenticação, API Gateway, importação de arquivos, envio de notificações, processamento assíncrono, e relatórios — com objetivo de identificar gargalos e permitir escalabilidade horizontal.

### Comparação Detalhada de Tecnologias

#### 1. Recursos de Segurança

| Recurso | Python | Go | Node.js | PHP |
|---------|--------|-----|---------|-----|
| Práticas de Codificação Segura | Bibliotecas robustas (bleach, sqlalchemy) | Seguro, mas requer mais validação manual | Requer validação manual, helmet.js para headers de segurança | Filtragem integrada (htmlspecialchars, addslashes) |
| Autenticação & Autorização | Django/Flask/FastAPI com auth integrado | Requer implementação manual (authboss, jwt-go) | passport.js, jsonwebtoken para OAuth2 & JWT | Laravel fornece auth & RBAC integrados |
| Criptografia de Dados | cryptography, PyNaCl | Pacote crypto na biblioteca padrão | Módulo crypto (menos recursos) | OpenSSL, Sodium, mcrypt (obsoleto) |
| CSRF & Headers de Segurança | Django/Flask com proteção CSRF integrada | Requer middleware personalizado | Proteção CSRF via csurf | Laravel e Symfony com proteção CSRF integrada |
| Gestão de Sessões | Django/Flask suportam gerenciamento de sessões | Integrado, leve | express-session ou sessões baseadas em JWT | Gerenciamento de sessões nativo em PHP |

**Destaque**: Python e PHP oferecem os melhores recursos de segurança integrados, enquanto Go e Node.js requerem mais implementações personalizadas.

#### 2. Desempenho e Concorrência

| Recurso | Python | Go | Node.js | PHP |
|---------|--------|-----|---------|-----|
| Velocidade | Mais lento devido à natureza interpretada | Rápido devido à execução compilada | Mais rápido que Python, mais lento que Go | Mais rápido que Python, mais lento que Go e Node.js |
| Multithreading | Single-thread com GIL, frameworks async ajudam | Goroutines nativas para concorrência | Async não-bloqueante (Event Loop) | PHP-FPM fornece manipulação de requisições concorrentes |
| Mecanismos de Cache | Integração Redis/Memcached | Cache em memória integrado | Mesmo que Python, mais node-cache | OPcache, APCu, Redis para cache |
| Processamento Assíncrono | asyncio, FastAPI para async | Goroutines permitem threading leve | Orientado a eventos, async/await e worker threads | PHP é síncrono, mas Laravel Queues & Swoole permitem processamento assíncrono |

**Destaque**: Go oferece a melhor concorrência e velocidade, enquanto Node.js e PHP (com PHP-FPM, Swoole) funcionam bem para aplicações web concorrentes.

#### 3. Infraestrutura e Implantação

| Recurso | Python | Go | Node.js | PHP |
|---------|--------|-----|---------|-----|
| Integração WAF | Funciona com AWS WAF, Cloudflare | Integra bem com CDN/WAF | Mesmo que Python, integra com Fastly, Cloudflare | Funciona com Cloudflare, AWS WAF, Imperva |
| Pipelines CI/CD | GitHub Actions, GitLab CI/CD | Mais fácil devido a binários compilados | Similar ao Python, usado com Docker | Integra bem com Jenkins, GitHub Actions |
| Balanceamento de Carga | Nginx, HAProxy | Funciona bem com balanceadores de carga em nuvem | Mesmo que Python, integra com AWS ALB | Funciona bem com Nginx, Apache, HAProxy |
| Auto Scaling | Kubernetes, AWS Auto Scaling | Leve e escala melhor que Python | Pode escalar, mas tem maior consumo de memória | Escala bem com Nginx, PHP-FPM |

**Destaque**: Go é leve e escalável, enquanto PHP é fácil de implantar em ambientes de hospedagem tradicionais.

#### 4. Usabilidade e Experiência do Desenvolvedor

| Recurso | Python | Go | Node.js | PHP |
|---------|--------|-----|---------|-----|
| Importação & Exportação de Arquivos | Suporta CSV, JSON, XML, Excel, PDF | Suporta os mesmos, mas requer mais análise manual | Módulo fs suporta CSV, JSON, XML | Suporte nativo para manipulação de arquivos no PHP core |
| Suporte Multi-idioma | i18n integrado em Django, Flask | Requer bibliotecas externas (golang.org/x/text) | i18next, react-intl para localização | Laravel, Symfony têm suporte i18n integrado |
| Notificações em Tempo Real | WebSockets via FastAPI, Django Channels | WebSockets com gorilla/websocket | WebSockets são nativos (socket.io) | Requer Swoole ou Ratchet para WebSockets |
| Design de API | Django REST Framework, FastAPI | Mais boilerplate necessário | Express.js, NestJS para APIs estruturadas | Laravel & Symfony têm suporte REST integrado |

**Destaque**: Python oferece mais usabilidade integrada, enquanto PHP é fácil para iniciantes mas requer pacotes de terceiros para WebSockets.

#### 5. Suporte a GraphQL

| Recurso | Python | Go | Node.js | PHP |
|---------|--------|-----|---------|-----|
| Biblioteca GraphQL | graphene, strawberry | gqlgen (mais rápido em Go) | Apollo Server, Express-GraphQL | webonyx/graphql-php (Laravel Lighthouse suporta GraphQL) |
| Desempenho | Moderado | Implementação GraphQL mais rápida | Alto desempenho com Apollo/Federation | Similar ao Python |
| Facilidade de Uso | Simples com integração Django/Flask | Requer boilerplate, mas rápido | Melhor ecossistema com Apollo | Requer Laravel ou Symfony para melhor experiência do desenvolvedor |

**Destaque**: Node.js tem o melhor ecossistema GraphQL, Go é o mais rápido, PHP e Python têm suporte sólido.

#### 6. Escalabilidade e Preparação para Nuvem

| Recurso | Python | Go | Node.js | PHP |
|---------|--------|-----|---------|-----|
| Escalabilidade Horizontal | Funciona bem no Kubernetes | Consome menos memória, mais fácil de escalar | Escala bem, mas requer balanceamento de carga | PHP-FPM escala bem, mas tem alto consumo de memória |
| Suporte a Microserviços | FastAPI & Flask suportam serviços leves | Go é mais adequado para microserviços | NestJS facilita microserviços | Laravel Octane ajuda microserviços PHP |
| Preparação Serverless | Funciona em AWS Lambda, Google Cloud | Cold starts são mais rápidos | Também funciona em AWS Lambda, mas maior uso de memória | AWS Lambda, Bref para PHP serverless |
| Consumo de Memória | Maior que Go & Node.js | Menor consumo de memória | Menor que Python, maior que Go | Uso moderado de memória, mas PHP-FPM é pesado |

**Destaque**: Go é o melhor para nuvem e microserviços, enquanto PHP é amplamente suportado em hospedagem tradicional e ambientes serverless.

## Decisão

Optamos por utilizar PHP com o framework Laravel como base do backend da aplicação.

### Justificativas principais:

#### Familiaridade e Produtividade
- A equipe possui ampla experiência com PHP e Laravel, o que garante agilidade no desenvolvimento, menor curva de aprendizado e facilidade de manutenção no longo prazo.
- PHP é ideal para desenvolvimento web tradicional, sistemas de e-commerce, CMS e blogs.

#### Maturidade do Ecossistema
- PHP possui uma comunidade sólida, com vasto conjunto de pacotes confiáveis e bem mantidos. 
- Laravel, por sua vez, oferece um ecossistema completo para APIs REST e GraphQL, filas assíncronas, autenticação, cache e muito mais.
- Hospedagem amplamente disponível e econômica para aplicações PHP.

#### Segurança
PHP e Laravel oferecem recursos nativos robustos:
- CSRF protection com tokens automáticos
- Escapamento de saída padrão (blade templates)
- Integração fácil com políticas de RBAC
- Suporte nativo à criptografia e proteção contra XSS
- Gestão de sessões segura e configurável

#### Flexibilidade de Arquitetura
- Laravel oferece suporte nativo à construção modular e desacoplada via providers, containers de injeção de dependência, middlewares e filas.
- Também permite separação clara entre domínios, facilitando a futura transição para microserviços, se necessário.
- PHP-FPM proporciona boa manipulação de requisições concorrentes.

#### Suporte a GraphQL
- Laravel pode ser facilmente integrado com Laravel Lighthouse, uma biblioteca madura que permite a construção de APIs GraphQL com autenticação, autorização, diretivas customizadas e integração com Eloquent ORM.
- Embora Node.js ofereça o melhor ecossistema GraphQL, o Laravel Lighthouse fornece uma experiência de desenvolvedor superior no contexto de uma aplicação Laravel.

## Comparação com Alternativas Consideradas

### Python
- **Vantagens**: Excelente para desenvolvimento de APIs, AI/ML, usabilidade e segurança.
- **Desvantagens**: Desempenho mais lento, menos otimizado para aplicações web tradicionais comparado ao PHP com Laravel.

### Go
- **Vantagens**: Melhor desempenho, concorrência e baixo consumo de memória, ideal para microserviços.
- **Desvantagens**: Curva de aprendizado mais íngreme para a equipe, ecossistema menos maduro para desenvolvimento web completo.

### Node.js
- **Vantagens**: Excelente para aplicações em tempo real, melhor ecossistema GraphQL.
- **Desvantagens**: Maior consumo de memória que Go, padrões de segurança menos integrados comparados ao PHP/Laravel.

## Consequências

A aplicação será construída em PHP com Laravel, utilizando REST e GraphQL (via Laravel Lighthouse) como protocolos principais de comunicação.

A arquitetura seguirá abordagem modular, permitindo que partes críticas da aplicação (como processamento de arquivos, autenticação, fila de mensagens, relatórios) sejam isoladas, testadas e otimizadas individualmente.

Serão utilizados Redis (cache e filas), PostgreSQL (dados relacionais), MongoDB (dados semiestruturados), além de RabbitMQ ou Laravel Horizon para controle de jobs assíncronos.

A escolha por PHP reduz riscos operacionais e acelera o desenvolvimento pela alta aderência da stack ao conhecimento da equipe, embora reconheçamos que:

- Para componentes específicos com alta demanda de concorrência, podemos considerar Go em implementações futuras.
- Para funcionalidades em tempo real, podemos integrar Node.js para WebSockets ou adotar Swoole para PHP.
- A separação modular permite que decisões tecnológicas específicas possam ser revisitadas para módulos individuais conforme necessário.