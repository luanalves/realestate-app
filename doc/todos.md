# TODOs - Real Estate App

## ✅ Concluído
- [x] Terminar consulta do detalhe de logs
- [x] Implementar testes unitários básicos no módulo de security

## 🔥 Prioridade ALTA

### Módulo Security - Testes GraphQL
- [ ] **Corrigir autenticação nos testes GraphQL**
  - Problema: Mock de autenticação com Passport::actingAs() falha
  - Solução: Usar factory de user real com role adequada (super_admin/real_estate_admin)
  - Arquivo: `tests/Feature/Security/SecurityLogGraphQLTest.php`

- [ ] **Adicionar seeders de teste para dados consistentes**
  - Criar dados de SecurityLog para testes
  - Garantir usuários com roles apropriadas existem
  - Dados MongoDB de exemplo para LogDetail

## 🔶 Prioridade MÉDIA

### Módulo Security - Completar Testes Faltantes
- [ ] **SecurityLogService integration tests**
  - Testes com database real para getStatistics()
  - Testes de filtros complexos
  - Testes de paginação com dados reais

- [ ] **Resolvers GraphQL unitários isolados**
  - SecurityLogQuery resolver individual
  - SecurityLogs resolver com mocks
  - SecurityLogStats resolver isolado
  - SecurityLogDetails resolver com MongoDB mock

- [ ] **Testes de autorização específicos**
  - Verificar roles super_admin e real_estate_admin têm acesso
  - Verificar roles client e real_estate_agent são negados
  - Testes de diferentes cenários de permissão

- [ ] **Testes de validação de entrada**
  - Validação de filtros inválidos
  - Validação de parâmetros de paginação
  - Validação de ordenação com colunas inexistentes

- [ ] **Documentar como validar a autenticação do cliente e validar a role dele**
  - Possível abstrair e criar um módulo específico para autorização
  - Middleware genérico para verificação de roles
  - Documentação de uso para outros módulos

## 📊 Status Atual do Módulo Security

```
✅ Middleware: 100% funcional (10/10 testes)
✅ Models: 100% funcional (8/8 testes) 
✅ Service (partial): 67% funcional (2/3 testes)
❌ GraphQL Resolvers: 0% funcional (0/7 testes)
❌ Integration Tests: 0% funcional (0/7 testes)

TOTAL: 69% dos testes funcionais (20/29)
```

**Meta:** Atingir 95%+ de cobertura de testes funcionais no módulo Security
