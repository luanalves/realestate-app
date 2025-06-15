# ADR [0015]: Adoção do Padrão Conventional Commits

**Data:** 2025-06-15  
**Status:** Aceito  
**Responsável:** Equipe de Desenvolvimento  

---

## Contexto

Durante o desenvolvimento da aplicação imobiliária, identificamos a necessidade de padronizar as mensagens de commit para melhorar:

- **Rastreabilidade**: Compreensão clara do que cada commit modifica
- **Automação**: Geração automática de CHANGELOGs e versionamento semântico
- **Colaboração**: Facilitar a entrada de novos desenvolvedores no projeto
- **Manutenibilidade**: Navegação mais eficiente no histórico do projeto
- **Code Review**: Melhor compreensão das mudanças durante revisões

O projeto estava utilizando mensagens de commit inconsistentes, dificultando a compreensão do escopo e impacto das mudanças realizadas.

---

## Decisão

Adotamos o **Conventional Commits Pattern** como padrão obrigatório para todas as mensagens de commit do projeto.

### Estrutura Obrigatória

```
<type>(<scope>): <subject>

[body]

[footer]
```

### Types Aprovados para o Projeto

**Core Types:**
- `feat`: Nova funcionalidade
- `fix`: Correção de bugs
- `refactor`: Refatoração sem mudança de funcionalidade
- `perf`: Melhoria de performance

**Auxiliary Types:**
- `docs`: Documentação
- `style`: Formatação/estilo de código
- `test`: Testes
- `chore`: Manutenção e dependências
- `build`: Sistema de build/dependências
- `ci`: Configuração de CI/CD
- `revert`: Reverter commit anterior

**Custom Types (Específicos do Projeto):**
- `config`: Alterações de configuração
- `security`: Melhorias de segurança

### Scopes Definidos

- `auth`: Autenticação e autorização
- `user`: Gestão de usuários
- `cache`: Sistema de cache
- `database`: Configurações de banco de dados
- `config`: Arquivos de configuração
- `graphql`: Schema e resolvers GraphQL
- `oauth`: Configurações OAuth/Passport
- `test`: Arquivos de teste
- `docs`: Documentação

### Regras de Subject

- **Imperativo**: usar "add", "fix", "update" (não "added", "fixed", "updated")
- **Minúscula**: sempre iniciar com letra minúscula
- **Sem ponto final**: não terminar com `.`
- **Máximo 50 caracteres**
- **Claro e descritivo**

### Regra de Validação

Todo commit deve completar a frase:
> "If applied, this commit will **\<subject\>**"

---

## Implementação

### Documentação

Criado guia completo em `doc/conventional-commits-guide.md` contendo:
- Estrutura detalhada dos commits
- Types e scopes específicos do projeto
- Templates práticos
- Exemplos corretos e incorretos
- Comandos práticos para uso diário

### Exemplo de Commits do Projeto

```bash
# Configurações
config(cache): set Redis as default cache driver
config(app): update domain to realestate.localhost

# Funcionalidades
feat(user): add UserRepositoryInterface contract
feat(user): implement CachedUserRepository with Redis
feat(cache): create UserRepositoryFactory with auto-detection

# Refatoração
refactor(auth): update login mutation to use UserService

# Comandos
feat(user): add UserCacheCommand for cache management
feat(oauth): add TokenAnalysisCommand for token monitoring

# Testes
test(user): add UserRepositoryFactory integration tests

# Limpeza
chore(routes): remove unused user_management.php routes
```

---

## Benefícios Esperados

1. **Histórico Legível**: Navegação clara no git log
2. **Automação Futura**: 
   - Geração automática de CHANGELOGs
   - Versionamento semântico automatizado
   - Release notes automáticas
3. **Onboarding**: Novos desenvolvedores compreendem rapidamente o projeto
4. **Code Review**: Revisões mais eficientes com contexto claro
5. **Debugging**: Localização rápida de mudanças que introduziram bugs
6. **Métricas**: Análise de tipos de mudanças (features vs fixes vs refactoring)

---

## Ferramentas de Apoio

### Recomendadas para Futuro
- **commitlint**: Validação automática de mensagens
- **conventional-changelog**: Geração de CHANGELOGs
- **commitizen**: Interface interativa para commits
- **semantic-release**: Versionamento e release automático

---

## Migração

### Commits Existentes
- Commits anteriores a esta ADR mantêm formato atual
- Novos commits devem seguir obrigatoriamente o padrão

### Treinamento
- Guia disponível em `doc/conventional-commits-guide.md`
- Exemplos práticos baseados no projeto real
- Templates para situações comuns

---

## Monitoramento

### Indicadores de Sucesso
- 100% dos commits novos seguindo o padrão
- Redução do tempo de code review
- Melhoria na compreensão do histórico do projeto
- Facilidade na geração de release notes

### Revisão
- Revisar eficácia após 30 dias de uso
- Coletar feedback da equipe
- Ajustar scopes conforme evolução do projeto

---

## Consequências

### Positivas
- **Consistência**: Padronização completa das mensagens
- **Automação**: Base para ferramentas de automação
- **Clareza**: Compreensão imediata do impacto das mudanças
- **Profissionalismo**: Projeto alinhado com padrões da indústria

### Negativas
- **Curva de Aprendizado**: Tempo inicial para adaptação
- **Rigor**: Necessidade de disciplina para manter o padrão
- **Overhead**: Pequeno tempo adicional na criação de commits

### Mitigação
- Guia detalhado com exemplos práticos
- Templates para situações comuns
- Possibilidade de ferramentas de validação automática

---

## Referências

- [Conventional Commits Official](https://www.conventionalcommits.org/)
- [Medium - Conventional Commits Pattern](https://medium.com/linkapi-solutions/conventional-commits-pattern-3778d1a1e657)
- [Chris Beams - Git Commit Guidelines](https://chris.beams.io/posts/git-commit/)
- [Guia do Projeto](doc/conventional-commits-guide.md)
