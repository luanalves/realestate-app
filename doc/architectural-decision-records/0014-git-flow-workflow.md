# ADR [0014]: Git Flow Workflow

**Status:** Aceito  
**Data:** 2025-06-14

## Contexto

O projeto Real Estate App possui uma arquitetura modular complexa com múltiplos desenvolvedores trabalhando em diferentes funcionalidades simultaneamente. É necessário estabelecer um workflow de versionamento que:

- Garanta estabilidade da branch principal (main)
- Permita desenvolvimento paralelo de features
- Facilite releases controladas
- Possibilite correções urgentes (hotfix) quando necessário
- Mantenha histórico claro e organizado
- Suporte integração contínua e deployment

Considerando que o projeto já possui:
- Testes unitários e de integração automatizados
- Arquitetura modular bem definida
- Padrões de código estabelecidos (PSR)
- Sistema de auditoria e logging
- Ambiente Docker para desenvolvimento

## Decisão

Adotaremos o **Git Flow** como estratégia de branching para organização do desenvolvimento, com as seguintes branches principais:

### Branches Principais

1. **main**: Branch de produção
   - Contém apenas código estável e testado
   - Todo commit deve representar uma versão deployável
   - Protegida contra push direto
   - Requer pull request com revisão de código

2. **develop**: Branch de desenvolvimento
   - Integra todas as features em desenvolvimento
   - Base para criação de novas features
   - Executada automaticamente nos testes de CI/CD

### Branches de Suporte

3. **feature/[nome-da-feature]**: Features em desenvolvimento
   - Criadas a partir da branch `develop`
   - Nomenclatura: `feature/user-preferences-module`, `feature/audit-system`, etc.
   - Merge de volta para `develop` via Pull Request

4. **release/[versão]**: Preparação de releases
   - Criadas a partir da branch `develop`
   - Nomenclatura: `release/1.0.0`, `release/1.1.0`, etc.
   - Apenas correções de bugs e ajustes finais
   - Merge para `main` e `develop`

5. **hotfix/[descrição]**: Correções urgentes
   - Criadas a partir da branch `main`
   - Nomenclatura: `hotfix/critical-security-fix`, `hotfix/oauth-token-issue`, etc.
   - Merge para `main` e `develop`

### Convenções de Nomenclatura

- **Features**: `feature/[modulo]-[funcionalidade]`
  - Exemplos: `feature/user-management-roles`, `feature/properties-search`
- **Releases**: `release/[major].[minor].[patch]`
  - Exemplos: `release/1.0.0`, `release/1.1.0`
- **Hotfixes**: `hotfix/[descrição-curta]`
  - Exemplos: `hotfix/oauth-security`, `hotfix/database-connection`

### Workflow de Desenvolvimento

1. **Nova Feature**:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/nome-da-feature
   # desenvolvimento
   git push origin feature/nome-da-feature
   # criar Pull Request para develop
   ```

2. **Release**:
   ```bash
   git checkout develop
   git checkout -b release/1.0.0
   # ajustes finais e testes
   git checkout main
   git merge release/1.0.0
   git tag v1.0.0
   git checkout develop
   git merge release/1.0.0
   ```

3. **Hotfix**:
   ```bash
   git checkout main
   git checkout -b hotfix/descrição
   # correção urgente
   git checkout main
   git merge hotfix/descrição
   git tag v1.0.1
   git checkout develop
   git merge hotfix/descrição
   ```

### Regras de Proteção

- **main**: Pull Request obrigatório + 1 aprovação + testes passando
- **develop**: Pull Request obrigatório + testes passando
- **Commits**: Mensagens claras seguindo padrão conventional commits
- **Tags**: Versionamento semântico (SemVer) para releases

## Consequências

### Impactos Positivos

- **Estabilidade**: Branch main sempre deployável
- **Organização**: Desenvolvimento paralelo sem conflitos
- **Rastreabilidade**: Histórico claro de features e releases
- **Qualidade**: Code review obrigatório via Pull Requests
- **Releases Controladas**: Processo formal de versionamento
- **Correções Urgentes**: Workflow específico para hotfixes

### Impactos Negativos

- **Complexidade**: Desenvolvedores precisam conhecer o workflow
- **Overhead**: Processo mais formal que simple branching
- **Configuração**: Necessário configurar proteções de branch no repositório

### Compromissos Aceitos

- Todos os desenvolvedores devem seguir o Git Flow rigorosamente
- Pull Requests são obrigatórios mesmo para pequenas alterações
- Releases seguem processo formal com branch específica
- Hotfixes interrompem o fluxo normal para correções críticas

### Implicações Futuras

- Facilita implementação de CI/CD baseado em branches
- Suporte a multiple environments (dev, staging, prod)
- Base para automação de releases e changelogs
- Integração com ferramentas de project management
- Preparação para migração futura para microserviços (cada módulo pode ter seu próprio repositório seguindo o mesmo padrão)

### Ferramentas Recomendadas

- **git-flow**: Extensão para automatizar comandos Git Flow
- **GitHub/GitLab**: Configuração de branch protection rules
- **Conventional Commits**: Padronização de mensagens de commit
- **Semantic Release**: Automação de versionamento baseado em commits
