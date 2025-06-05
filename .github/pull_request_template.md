## 📝 Descrição

Descreva brevemente as mudanças implementadas neste PR.

## 🎯 Tipo de Mudança

- [ ] 🐛 Bug fix (correção de problema)
- [ ] ✨ Nova funcionalidade
- [ ] 💥 Breaking change (mudança que quebra compatibilidade)
- [ ] 📚 Documentação
- [ ] 🔧 Refatoração
- [ ] 🧪 Testes
- [ ] 🔒 Segurança

## 📋 Checklist - Validações Obrigatórias

### **Arquitetura e Estrutura**
- [ ] Código segue a arquitetura modular (ADR-0002)
- [ ] Arquivos estão no módulo correto (`modules/NomeModulo/`)
- [ ] Namespaces seguem convenção `Modules\NomeModulo\`
- [ ] Não há dependências circulares entre módulos

### **Padrões de Código (ADR-0006)**
- [ ] Código segue PSR-12
- [ ] Todos os arquivos PHP incluem `declare(strict_types=1);`
- [ ] Header de copyright presente em todos os arquivos
- [ ] Nomenclaturas seguem convenções definidas

### **🗄️ Banco de Dados (ADR-0013) - OBRIGATÓRIO**
- [ ] **Migrations incluem `$table->timestamps()`**
- [ ] **Models incluem casts para `created_at` e `updated_at`**
- [ ] Índices apropriados criados para campos de timestamp
- [ ] Comentários explicativos em campos importantes

**Se não aplicável, justifique:**
```
<!-- Justificativa para omitir timestamps (se aplicável):
Exemplo: "Tabela de lookup estática que não requer rastreamento temporal"
-->
```

### **GraphQL e API (ADR-0003)**
- [ ] Funcionalidades implementadas via GraphQL (não REST)
- [ ] Schema GraphQL definido antes da implementação
- [ ] Uso correto de diretivas Lighthouse
- [ ] Resolvers em diretórios apropriados

### **Autenticação e Autorização (ADR-0004)**
- [ ] Endpoints protegidos usam `@auth` directive
- [ ] Verificação de permissões baseada em roles
- [ ] Não há exposição de dados sensíveis

### **🧪 Testes (ADR-0011)**
- [ ] Testes para queries GraphQL
- [ ] Testes para mutations GraphQL
- [ ] Testes de validação e casos de erro
- [ ] Testes de autenticação/autorização
- [ ] Testes organizados por módulo
- [ ] Uso de mocks para isolamento

### **🔒 Segurança (ADR-0009, ADR-0012)**
- [ ] Operações sensíveis são auditadas
- [ ] Não há vazamento de dados sensíveis
- [ ] Input validation implementada
- [ ] Sanitização de dados de entrada

### **📝 Documentação**
- [ ] Métodos complexos têm docblocks
- [ ] Constantes documentadas
- [ ] README do módulo atualizado (se aplicável)
- [ ] ADRs consultadas e respeitadas

## 🧪 Testes Executados

```bash
# Comandos executados para validar as mudanças:
cd ../realestate-infra && docker compose exec app php artisan test
cd ../realestate-infra && docker compose exec app ./vendor/bin/pint --test

# Resultados:
# ✅ Todos os testes passaram
# ✅ Padrões de código validados
```

## 🗄️ Migrations e Models

### **Se este PR inclui migrations, confirme:**
- [ ] Migration testada localmente
- [ ] Rollback testado (`php artisan migrate:rollback`)
- [ ] Dados de exemplo populados para teste
- [ ] Performance da migration avaliada

### **Se este PR inclui models, confirme:**
- [ ] Relacionamentos testados
- [ ] Casts apropriados definidos
- [ ] Factory criada/atualizada (se necessário)

## 📚 ADRs Relacionadas

Liste as ADRs que se aplicam a este PR:
- [ ] ADR-0002: Arquitetura Modular
- [ ] ADR-0003: GraphQL com Lighthouse
- [ ] ADR-0006: Padrões de Código e PSR
- [ ] ADR-0011: Padronização de Testes
- [ ] ADR-0013: Convenções de Banco de Dados
- [ ] Outra: _______________

## 🔍 Como Testar

Descreva os passos para testar as funcionalidades implementadas:

1. **Setup:**
   ```bash
   cd ../realestate-infra && docker compose exec app php artisan migrate
   ```

2. **Teste via GraphQL Playground:**
   - Acesse: http://localhost:8080/graphql-playground
   - Execute a query/mutation: 
   ```graphql
   # Cole aqui a query/mutation de exemplo
   ```

3. **Resultados Esperados:**
   - Descreva o comportamento esperado

## 📸 Screenshots (se aplicável)

<!-- Adicione screenshots se a mudança afeta a UI ou tem resultado visual -->

## ⚠️ Breaking Changes

Se este PR contém breaking changes, descreva:
- O que foi alterado
- Como migrar código existente
- Impacto em outros módulos

## 📝 Notas Adicionais

<!-- Informações extras, contexto adicional, links relacionados, etc. -->

---

## ✅ Aprovação Final

**Para o reviewer:**
- [ ] Todos os itens do checklist foram validados
- [ ] Testes executados com sucesso
- [ ] Código revisado linha por linha
- [ ] Documentação adequada
- [ ] Sem impactos negativos identificados

**Comando de validação final:**
```bash
# Verificar migrations sem timestamps
grep -r "Schema::create" modules/*/Database/Migrations/ | xargs grep -L "timestamps()"

# Executar todos os testes
cd ../realestate-infra && docker compose exec app php artisan test
```
