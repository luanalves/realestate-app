# TODOs - Real Estate App

## ✅ Concluído
- [x] Terminar consulta do detalhe de logs
- [x] Implementar testes unitários básicos no módulo de security
- [x] **Documentar como validar a autenticação do cliente e validar a role dele**
  - [x] Criado Authorization Service Pattern
  - [x] Documentação completa em `doc/patterns/authorization-service-pattern.md`
  - [x] Implementado SecurityAuthorizationService
  - [x] Implementado UserManagementAuthorizationService
  - [x] Refatorados todos os resolvers GraphQL para usar os serviços
  - [x] Eliminada duplicação de código em 7+ arquivos
  - [x] Substituídas strings mágicas por constantes de roles
  - [x] Criados testes unitários para os serviços de autorização

## 🔥 Prioridade ALTA

### Authorization Service Pattern - Expansão
- [x] **Identificar todos os módulos existentes**
  - ✅ Security: Implementado
  - ✅ UserManagement: Implementado
  - ✅ Apenas 2 módulos existem no projeto

- [ ] **Middleware GraphQL para autorização automática**
  - Criar middleware que aplica autorização baseada em anotações
  - Integrar com Lighthouse GraphQL
  - Documentar uso nos schemas GraphQL

- [ ] **Melhorar documentação do padrão**
  - Adicionar exemplos de uso em diferentes contextos
  - Documentar boas práticas de teste
  - Criar guia de implementação para novos módulos

### Módulo Security - Testes GraphQL
- [ ] **Corrigir autenticação nos testes GraphQL**
  - Problema: Mock de autenticação com Passport::actingAs() falha
  - Solução: Usar factory de user real com role adequada (super_admin/real_estate_admin)
  - Arquivo: `tests/Feature/Security/SecurityLogGraphQLTest.php`

- [ ] **Adicionar seeders de teste para dados consistentes**
  - Criar dados de SecurityLog para testes
  - Garantir usuários com roles apropriadas existem
  - Dados MongoDB de exemplo para LogDetail

### Módulo UserManagement - Funcionalidades Essenciais
- [ ] **Gestão de Senha**
  - [ ] Implementar mutation para alteração de senha (changePassword)
  - [ ] Implementar fluxo de recuperação de senha (requestPasswordReset, resetPassword)
  - [ ] Testes para alteração e recuperação de senha
- [ ] **Associação Multi-Tenant (Imobiliárias)**
  - [ ] Garantir campo tenant_id em users
  - [ ] Restringir queries/mutations por tenant_id (exceto Master Admin)
  - [ ] Testes de acesso multi-tenant
- [ ] **Dados de Perfil**
  - [ ] Query para visualização de perfil (me)
  - [ ] Mutation para edição de perfil (updateProfile)
  - [ ] Mutation para upload de avatar (uploadAvatar)
  - [ ] Mutation para preferências pessoais (updatePreferences)
  - [ ] Testes de perfil e preferências
- [ ] **Listagem e Gerenciamento de Usuários (Backoffice)**
  - [ ] Query para listar usuários por imobiliária (usersByTenant)
  - [ ] Mutation para ativar/inativar usuário (setUserActiveStatus)
  - [ ] Mutation para resetar senha de usuário (adminResetUserPassword)
  - [ ] Testes de gerenciamento de usuários

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



## 📊 Status Atual do Projeto

### Módulo Security
```
✅ Middleware: 100% funcional (10/10 testes)
✅ Models: 100% funcional (8/8 testes) 
✅ Service (partial): 67% funcional (2/3 testes)
✅ Authorization Service: 100% funcional (novo)
❌ GraphQL Resolvers: 0% funcional (0/7 testes)
❌ Integration Tests: 0% funcional (0/7 testes)

TOTAL: 75% dos testes funcionais
```

### Módulo UserManagement
```
✅ Authorization Service: 100% funcional (novo)
✅ Existing Tests: 100% funcional (83/83 testes)
✅ Refactored Resolvers: 100% funcional (5/5 resolvers)

TOTAL: 100% dos testes funcionais
```

### Authorization Service Pattern
```
✅ Security Module: Implementado
✅ UserManagement Module: Implementado
✅ Documentation: Completa (doc/patterns/)
✅ Module Coverage: 100% (2/2 módulos existentes)
❌ Middleware Integration: Pendente

TOTAL: 80% implementado (4/5 tarefas)
```

**Meta:** Atingir 95%+ de cobertura de testes funcionais em todos os módulos

## 🎯 Próximos Marcos

1. **Completar Authorization Service Pattern** (expandir para todos os módulos)
2. **Resolver testes GraphQL** do módulo Security  
3. **Implementar middleware GraphQL** para autorização automática
4. **Documentar outros padrões** identificados no projeto

## Observações Técnicas
- O model `User` deve conter o campo `tenant_id` para associação multi-tenant.
- Todos os acessos (queries e mutations) devem ser protegidos com middleware do tipo `auth` e `can` (autorização baseada em permissões/roles).
