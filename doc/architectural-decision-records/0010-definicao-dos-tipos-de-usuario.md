# ADR [0010]: Definição dos Tipos de Usuário

**Status:** Aceito  
**Data:** 2025-03-10

## Contexto

Durante o desenvolvimento do sistema imobiliário, identificamos a necessidade de definir claramente os diferentes tipos de usuários que interagirão com a plataforma. Esta definição é fundamental para:

- Estabelecer um modelo de permissões coerente e escalável
- Orientar o desenvolvimento de interfaces específicas para cada perfil
- Garantir que as funcionalidades implementadas atendam às necessidades específicas de cada tipo de usuário
- Fundamentar o modelo de autenticação e autorização (OAuth2 e RBAC) conforme definido no ADR-0004

A definição clara dos tipos de usuário também impacta diretamente em:
- Modelo de dados e relacionamentos
- Fluxos de negócio e casos de uso
- Requisitos de segurança e privacidade
- Experiência do usuário adaptada para cada perfil

## Decisão

Baseado na análise do domínio do negócio e nos requisitos funcionais, decidimos implementar quatro tipos principais de usuários no sistema, cada um com responsabilidades, objetivos e jornadas específicas:

### 1. Administrador da Aplicação (Master Admin)

**Descrição:** Responsável por gerenciar o sistema como um todo. Atua como o "superusuário", geralmente da empresa que fornece o sistema para várias imobiliárias.

**Objetivo:** Manter o sistema rodando, organizado e operando para todas as imobiliárias.

**Responsabilidades:**
- Criar e gerenciar contas de imobiliárias
- Definir limites de planos (número de imóveis, usuários, etc.)
- Monitorar uso e performance do sistema
- Suporte técnico de segundo nível
- Gerenciar configurações globais e integrações externas

**Funcionalidades:**
- Painel de controle global
- Acesso a todas as informações do sistema
- Logs, auditoria e analytics de uso
- Sistema de billing, planos, upgrades

**Jornada Típica:**
1. Login no painel master
2. Visualizar todas as imobiliárias cadastradas
3. Criar uma nova imobiliária
4. Definir limites, plano e integrações
5. Monitorar uso e performance geral
6. Gerar relatórios globais
7. Acessar logs e eventos de auditoria
8. Fornecer suporte técnico a usuários

### 2. Imobiliária (Gestor da Unidade)

**Descrição:** Usuário responsável pela conta da imobiliária. Pode ser o dono, gerente ou gestor operacional.

**Objetivo:** Gerir imóveis, leads e colaboradores da unidade.

**Responsabilidades:**
- Criar e gerenciar contas dos colaboradores
- Cadastrar e gerenciar imóveis
- Acompanhar leads e agendamentos
- Gerenciar contratos, pagamentos e comissões
- Customizar as configurações da própria imobiliária (logo, site, integrações, etc.)

**Funcionalidades:**
- Dashboard da unidade
- Gestão de equipe (funcionários/corretores)
- CRM de leads
- Relatórios de desempenho
- Integrações com portais e ferramentas externas

**Jornada Típica:**
1. Login na área da imobiliária
2. Cadastrar corretores e equipe
3. Cadastrar imóveis
4. Integrar com portais externos (Zap, OLX etc.)
5. Visualizar leads recebidos
6. Atribuir leads aos corretores
7. Acompanhar negociações e visitas
8. Gerar relatórios de desempenho da equipe
9. Criar e gerenciar contratos
10. Processar pagamentos, comissões e repasses

### 3. Funcionário/Corretor da Imobiliária

**Descrição:** Usuários operacionais com acesso limitado conforme o papel (ex: corretor, recepcionista, financeiro).

**Objetivo:** Converter leads em vendas/aluguéis.

**Possíveis Perfis:**
- Corretor: Focado na captação de imóveis, atendimento e conversão de leads
- Assistente: Auxilia nas agendas e no cadastro de dados
- Financeiro: Cuida de comissões e contratos

**Funcionalidades:**
- Visualizar e gerenciar seus próprios leads
- Agendar e registrar visitas
- Acompanhar imóveis cadastrados
- Atualizar status de negociações
- Emitir contratos e acompanhar pagamentos (se autorizado)

**Jornada Típica:**
1. Login
2. Ver imóveis da carteira
3. Buscar imóveis para indicar a clientes
4. Receber e visualizar leads
5. Entrar em contato com leads
6. Agendar visitas
7. Registrar status da negociação
8. Solicitar emissão de contrato

### 4. Cliente Final (Comprador/Locatário)

**Descrição:** Usuário externo, interessado na compra ou locação de imóveis. Acessa o frontend público ou uma área logada própria.

**Objetivo:** Encontrar um imóvel e fechar negócio.

**Funcionalidades:**
- Visualizar imóveis e enviar interesse
- Agendar visita
- Acompanhar propostas e negociações
- Assinar contratos eletronicamente
- Área logada com histórico e documentos

**Jornada Típica:**
1. Navegar no portal
2. Buscar imóveis por filtros
3. Enviar interesse (lead)
4. Aguardar contato da imobiliária
5. Agendar visita
6. Assinar contrato
7. Acompanhar andamento (se tiver área logada)

## Hierarquia de Permissões

Estabelecemos uma hierarquia clara de permissões entre os tipos de usuários:

1. **Administrador da Aplicação**: Acesso completo a todas as funcionalidades do sistema
2. **Gestor da Imobiliária**: Acesso completo à própria imobiliária e todas as suas operações
3. **Funcionário/Corretor**: Acesso limitado conforme configurado pelo gestor da imobiliária
4. **Cliente Final**: Acesso apenas às funcionalidades públicas e à sua própria área pessoal

Esta hierarquia será implementada através do sistema RBAC conforme definido no ADR-0004.

## Matriz de Permissões

Para garantir uma implementação consistente do controle de acesso, definimos uma matriz de permissões detalhada que associa cada tipo de usuário às ações que pode realizar em cada módulo do sistema:

| Módulo / Ação             | Administrador da Aplicação | Gestor Imobiliária      | Funcionário (Corretor)    | Cliente Final         |
|---------------------------|----------------------------|--------------------------|---------------------------|------------------------|
| Gerenciar imobiliárias    | ✅ CRUD                    | ❌                       | ❌                        | ❌                     |
| Gerenciar usuários        | ✅ CRUD                    | ✅ CRUD (da sua)         | ❌                        | ❌                     |
| Gerenciar imóveis         | ✅ R                       | ✅ CRUD                  | ✅ CRU (próprios)         | 🔍 (somente leitura)   |
| Buscar imóveis            | ✅                         | ✅                       | ✅                        | ✅                     |
| Gerenciar leads           | ❌                         | ✅ CRUD                  | ✅ CRU (designados)       | ❌                     |
| Gerenciar contratos       | ❌                         | ✅ CRUD                  | ✅ R (participantes)      | ✅ R (se vinculado)    |
| Gerenciar pagamentos      | ❌                         | ✅ CRUD                  | 🔍 (visualização parcial) | ✅ R (se vinculado)    |
| Relatórios e analytics    | ✅ completo                | ✅ (da sua unidade)      | 🔍 (parcial)              | ❌                     |
| Definir planos e billing  | ✅                         | ❌                       | ❌                        | ❌                     |
| Configurações gerais      | ✅                         | ✅ (somente da unidade)  | ❌                        | ❌                     |
| Integrações com portais   | ✅                         | ✅                       | ❌                        | ❌                     |
| Assinar contratos digitais| ❌                         | ✅                       | ✅                        | ✅                     |

**Legenda**:
- ✅ = Permissão total
- ❌ = Sem permissão
- 🔍 = Acesso restrito/somente leitura
- "próprios" ou "designados" = Acesso limitado a recursos vinculados diretamente ao usuário

## Associação de Módulos às Personas

Com base nas jornadas de usuário, os módulos do sistema são associados primariamente às seguintes personas:

| Módulo                | Persona Principal           | Jornada Envolvida                                    |
|-----------------------|-----------------------------|------------------------------------------------------|
| Imobiliárias          | Admin                       | Gestão de contas e limites por unidade               |
| Usuários              | Admin, Gestor               | Criação de contas, permissões, times                 |
| Imóveis               | Gestor, Corretor, Cliente   | Cadastro, busca, associação a leads                  |
| Leads                 | Gestor, Corretor            | Recepção, atribuição, acompanhamento de interesse    |
| Agenda / Visitas      | Corretor, Cliente           | Marcação e registro de visitas                       |
| Contratos             | Gestor, Corretor, Cliente   | Emissão, assinatura, acompanhamento                  |
| Pagamentos            | Gestor, Financeiro          | Repasse, comissão, integrações com gateways          |
| Relatórios            | Admin, Gestor               | Visualização de KPIs, conversão, desempenho          |
| Auditoria / Logs      | Admin                       | Eventos, rastreabilidade, segurança                  |
| Portal Público        | Cliente                     | Busca de imóveis, envio de lead, filtro, mapas       |

Esta associação de módulos às personas serve como guia para o desenvolvimento, indicando quais funcionalidades devem receber atenção prioritária em cada interface específica de usuário e como as jornadas se mapeiam para a arquitetura modular da aplicação.

## Consequências

A definição destes tipos de usuários tem as seguintes implicações para o sistema:

### Positivas:
- **Modelo de segurança claro**: Com papéis bem definidos, o sistema de autorização pode ser implementado de forma mais precisa e segura
- **UX personalizada**: Interfaces específicas por tipo de usuário melhoram a experiência e reduzem a complexidade
- **Escalabilidade do modelo de negócio**: Suporte a múltiplas imobiliárias com colaboradores e administração central
- **Melhor organização do código**: Módulos e funcionalidades podem ser organizados por tipo de usuário
- **Rastreabilidade e auditoria**: Ações específicas de cada tipo de usuário podem ser monitoradas adequadamente

### Desafios:
- **Complexidade do sistema de permissões**: Será necessário um sistema RBAC robusto para gerenciar diferentes níveis de permissão
- **Fluxos de autenticação múltiplos**: Cada tipo de usuário pode exigir fluxos de login e recuperação de senha específicos
- **Manutenção da consistência de interfaces**: Garantir que mudanças no sistema sejam refletidas em todas as interfaces relevantes
- **Gestão de dados compartilhados**: Imóveis e clientes podem ser acessados por múltiplos usuários com diferentes níveis de permissão

### Implementação Técnica:
- Os tipos de usuário serão implementados usando Laravel Passport para autenticação OAuth2
- As permissões seguirão o modelo RBAC definido no ADR-0004
- As interfaces GraphQL terão diretivas de autorização (@auth) específicas por tipo de usuário
- O módulo de UserManagement será responsável por gerenciar os diferentes tipos de usuário e suas permissões

Esta estrutura de usuários estabelece a base para o desenvolvimento dos módulos do sistema e orientará as decisões de implementação das funcionalidades específicas para cada perfil.