# ADR [0008]: Definição dos Domínios da Aplicação

**Status:** Aceito  
**Data:** 2025-03-24

## Contexto

Esta ADR complementa a decisão registrada na [ADR 0002 - Arquitetura Modular com Evolução para Microserviços](0002-arquitetura-modular-com-evolucao-para-microservicos.md), onde definimos que a aplicação será construída com base em módulos organizados por domínios de negócio.

Durante a modelagem da arquitetura da aplicação imobiliária, ficou evidente que a definição clara desses domínios é fundamental para garantir:

- Alta coesão e baixo acoplamento entre funcionalidades;
- Otimização independente de performance por módulo;
- Facilidade de evolução incremental e testes modulares;
- Preparação natural para uma futura extração de microserviços.

Essa divisão impacta diretamente a estrutura do código (um diretório por módulo), o schema GraphQL (com resolvers organizados por domínio) e o controle de acesso granular baseado em contexto.

## Decisão

Os domínios funcionais definidos inicialmente são:

### 1. **Auth (Autenticação e Autorização)**
- Login, logout, controle de sessão e permissões
- Integração com RBAC (controle de acesso baseado em papéis)
- Suporte atual e futuro a OAuth2, SSO e autenticação multifator (2FA)

### 2. **User Management (Gestão de Usuários)**
- Cadastro, edição e ativação de usuários
- Associação a papéis, perfis e permissões
- Separação entre perfis públicos (cliente) e operadores internos

### 3. **Property (Gestão de Imóveis)**
- CRUD de imóveis com vínculo a mídia (fotos, vídeos, documentos)
- Status de publicação (disponível, alugado, vendido, etc.)

### 4. **Opportunity (Gestão de Oportunidades)**
- Registro de interesses e propostas
- Histórico de negociações
- Integração futura com CRMs ou motores de scoring

### 5. **File Management (Importação e Exportação)**
- Upload e download de arquivos estruturados
- Validação, templates e logs de importação/exportação
- Processamento assíncrono com filas

### 6. **Notification (Notificações e Mensagens)**
- Envio de e-mail, push, SMS e mensagens internas
- Templates e agendamentos com suporte a filas

### 7. **Analytics & Reporting**
- Painéis analíticos (vendas, visitas, conversões)
- Relatórios exportáveis (PDF, CSV)
- Integração futura com data warehouses ou réplicas analíticas

### 8. **GraphQL API Gateway**
- Ponto único de entrada da API
- Gerenciamento de autenticação, autorização, logs e observabilidade
- Rate limiting por domínio ou operação

### 9. **Admin Panel (Painel Administrativo)**
- Interface para gestão interna da plataforma
- Acesso restrito a operadores via RBAC
- Acoplado aos módulos Auth e User Management

## Consequências

- A aplicação será estruturada em módulos isolados com escopos bem definidos.
- Cada domínio conterá seus próprios models, policies, resolvers, serviços e validações.
- A escalabilidade técnica e organizacional será facilitada pela separação de contexto.
- As integrações entre domínios ocorrerão por meio de serviços internos desacoplados, preservando a autonomia de cada módulo.

Essa decisão fortalece o alinhamento entre a arquitetura técnica e o modelo de negócio, preparando a base para um crescimento sustentável, seguro e escalável da aplicação.