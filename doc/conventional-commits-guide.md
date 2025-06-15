# Guia de Conventional Commits para GitHub Copilot

## 📋 Estrutura Básica

```
<type>(<scope>): <subject>

[body]

[footer]
```

**Obrigatórios:** `type` e `subject`  
**Opcionais:** `scope`, `body` e `footer`

---

## 🏷️ Types de Commit

### Core Types (Principais)

| Type | Uso | Exemplo |
|------|-----|---------|
| `feat` | Nova funcionalidade | `feat(auth): add user login with OAuth2` |
| `fix` | Correção de bugs | `fix(cache): resolve Redis connection timeout` |
| `refactor` | Refatoração sem mudança de funcionalidade | `refactor(user): extract validation logic to service` |
| `perf` | Melhoria de performance | `perf(database): optimize user queries with indexes` |

### Auxiliary Types (Auxiliares)

| Type | Uso | Exemplo |
|------|-----|---------|
| `docs` | Documentação | `docs(readme): update installation instructions` |
| `style` | Formatação/estilo | `style(format): fix code indentation` |
| `test` | Testes | `test(user): add unit tests for UserService` |
| `chore` | Manutenção | `chore(deps): update Laravel to 11.x` |
| `build` | Sistema de build/dependências | `build(docker): add Redis container configuration` |
| `ci` | Configuração de CI/CD | `ci(github): add automated testing workflow` |
| `revert` | Reverter commit anterior | `revert: feat(auth): add user login with OAuth2` |

### Custom Types (Específicos do Projeto)

| Type | Uso | Exemplo |
|------|-----|---------|
| `config` | Configurações | `config(cache): set Redis as default cache driver` |
| `security` | Melhorias de segurança | `security(auth): add rate limiting to login` |

---

## 🎯 Scope (Contexto)

### Scopes Principais do Projeto

- `auth` - Autenticação e autorização
- `user` - Gestão de usuários  
- `cache` - Sistema de cache
- `database` - Configurações de banco de dados
- `config` - Arquivos de configuração
- `graphql` - Schema e resolvers GraphQL
- `oauth` - Configurações OAuth/Passport
- `test` - Arquivos de teste
- `docs` - Documentação

### Múltiplos Scopes
```
feat(user,cache): implement user repository with cache layer
```

---

## ✍️ Subject (Mensagem)

### ✅ Boas Práticas

- **Imperativo**: "add", "fix", "update" (não "added", "fixed", "updated")
- **Minúscula**: sempre iniciar com letra minúscula
- **Sem ponto final**: não terminar com `.`
- **Máximo 50 caracteres**
- **Claro e descritivo**

### ✅ Exemplos Corretos
```
feat(auth): add OAuth2 password grant authentication
fix(cache): resolve user data invalidation on update
refactor(user): extract repository logic to service layer
config(app): update domain to realestate.localhost
```

### ❌ Exemplos Incorretos
```
feat(auth): Added OAuth2 authentication.  # pretérito + ponto final
Fix cache bug                            # não segue padrão + vago
Updated stuff                            # vago + pretérito
feat: some changes                       # muito vago
```

---

## 📋 Regra do "If Applied"

Todo commit deve completar a frase:
> "If applied, this commit will **\<subject\>**"

**Exemplos:**
- "If applied, this commit will **add OAuth2 password grant authentication**" ✅
- "If applied, this commit will **added OAuth2 authentication**" ❌

---

## 🚀 Templates para Commits Comuns

### Novas Funcionalidades
```
feat(<scope>): add <functionality>
feat(<scope>): implement <feature>
feat(<scope>): create <component>
```

### Correções
```
fix(<scope>): resolve <issue>
fix(<scope>): handle <error-case>
fix(<scope>): correct <problem>
```

### Refatoração
```
refactor(<scope>): extract <logic> to <destination>
refactor(<scope>): rename <old-name> to <new-name>
refactor(<scope>): reorganize <structure>
```

### Configuração
```
config(<scope>): set <setting> to <value>
config(<scope>): update <configuration>
config(<scope>): configure <service>
```

### Testes
```
test(<scope>): add unit tests for <component>
test(<scope>): add integration tests for <feature>
test(<scope>): update test for <functionality>
```

---

## 📝 Exemplo Prático para o Projeto Atual

### Estrutura dos Commits para Cache Implementation

```bash
# 1. Configurações
config(cache): set Redis as default cache driver
config(app): update domain to realestate.localhost
config(database): set PostgreSQL as default connection

# 2. Contratos e Interfaces
feat(user): add UserRepositoryInterface contract

# 3. Implementações
feat(user): implement CachedUserRepository with Redis
feat(user): implement DatabaseUserRepository fallback
feat(user): create UserRepositoryFactory with auto-detection

# 4. Serviços
feat(user): add UserService with repository injection
refactor(auth): update login mutation to use UserService

# 5. Comandos
feat(user): add UserCacheCommand for cache management
feat(oauth): add TokenAnalysisCommand for token monitoring

# 6. Testes
test(user): add UserRepositoryFactory integration tests

# 7. Cleanup
chore(routes): remove unused user_management.php routes
```

---

## 🔧 Comandos Práticos

### Verificar Status
```bash
git status
```

### Adicionar Arquivos Específicos
```bash
git add <file-path>
```

### Commit com Mensagem
```bash
git commit -m "feat(cache): implement user repository with Redis cache"
```

### Verificar Histórico
```bash
git log --oneline
```

---

## ⚠️ Regras Importantes

1. **Um tipo por commit**: Nunca misturar `feat` com `fix` no mesmo commit
2. **Commits atômicos**: Uma mudança lógica por commit
3. **Scope opcional mas recomendado**: Ajuda na organização
4. **Breaking changes**: Usar `!` após o tipo: `feat!: change user API structure`
5. **Commits grandes**: Se difícil de descrever, provavelmente deve ser dividido

---

## 🎨 Ferramentas Recomendadas

- **commitlint**: Valida mensagens automaticamente
- **conventional-changelog**: Gera CHANGELOGs automaticamente
- **commitizen**: Interface interativa para commits

---

## 📚 Referências

- [Conventional Commits Official](https://www.conventionalcommits.org/)
- [Chris Beams - Git Commit Guidelines](https://chris.beams.io/posts/git-commit/)
- [Medium - Conventional Commits Pattern](https://medium.com/linkapi-solutions/conventional-commits-pattern-3778d1a1e657)
