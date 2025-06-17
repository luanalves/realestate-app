# Diretrizes para Análise de Pull Request (PR)

## Objetivo
Este documento estabelece as diretrizes obrigatórias para análise de Pull Requests no projeto de aplicação imobiliária, garantindo qualidade, consistência e aderência às Architectural Decision Records (ADRs).

## Checklist Geral de PR

### 📋 **Validações Obrigatórias**

#### **1. Arquitetura e Estrutura**
- [ ] Código segue a arquitetura modular definida (ADR-0002)
- [ ] Novos arquivos estão no módulo correto (`modules/NomeModulo/`)
- [ ] Namespaces seguem a convenção `Modules\NomeModulo\`
- [ ] Não há dependências circulares entre módulos

#### **2. Padrões de Código (ADR-0006)**
- [ ] Código segue PSR-12 (Extended Coding Style Guide)
- [ ] Todos os arquivos PHP incluem `declare(strict_types=1);`
- [ ] Header de copyright presente em todos os arquivos
- [ ] Nomes de classes, métodos e variáveis seguem convenções definidas

#### **3. GraphQL e API (ADR-0003)**
- [ ] Novas funcionalidades são implementadas via GraphQL (não REST)
- [ ] Schema GraphQL definido antes da implementação
- [ ] Uso correto de diretivas Lighthouse (`@auth`, `@find`, etc.)
- [ ] Resolvers personalizados em diretórios apropriados

#### **4. Autenticação e Autorização (ADR-0004)**
- [ ] Endpoints protegidos usam `@auth` directive
- [ ] Verificação de permissões baseada em roles
- [ ] Não há exposição de dados sensíveis

## 🗄️ **Validações de Banco de Dados (ADR-0013)**

### **Migrations - OBRIGATÓRIO**
- [ ] **Todas as migrations de criação de tabela incluem `$table->timestamps()`**
- [ ] Campos `created_at` e `updated_at` estão presentes
- [ ] Índices apropriados foram criados para campos de timestamp
- [ ] Comentários explicativos em campos importantes

```php
// ✅ CORRETO
Schema::create('nome_tabela', function (Blueprint $table) {
    $table->id();
    // ... outros campos ...
    $table->timestamps(); // ← OBRIGATÓRIO
    
    // Índices recomendados
    $table->index(['created_at']);
    $table->index(['user_id', 'created_at']);
});
```

### **Models - OBRIGATÓRIO**
- [ ] **Todos os models incluem casts para `created_at` e `updated_at`**
- [ ] Campos de data/hora têm cast apropriado (`datetime`)
- [ ] Relacionamentos definidos corretamente

```php
// ✅ CORRETO
protected $casts = [
    'created_at' => 'datetime', // ← OBRIGATÓRIO
    'updated_at' => 'datetime', // ← OBRIGATÓRIO
    // ... outros casts ...
];
```

### **Exceções Permitidas**
Apenas nas seguintes situações os timestamps podem ser omitidos:
- [ ] Tabela de lookup/referência (justificativa necessária)
- [ ] Tabela de log imutável (justificativa necessária)
- [ ] Tabela pivô simples (justificativa necessária)

## 🧪 **Testes (ADR-0011)**

### **Cobertura Obrigatória**
- [ ] Testes para todas as queries GraphQL
- [ ] Testes para todas as mutations GraphQL
- [ ] Testes de validação e casos de erro
- [ ] Testes de autenticação/autorização

### **Estrutura de Testes**
- [ ] Testes organizados por módulo em `tests/Feature/NomeModulo/`
- [ ] Uso de mocks para isolamento de testes
- [ ] Autenticação via `Passport::actingAs()` em testes
- [ ] Nomenclatura descritiva dos métodos de teste

## 🔒 **Segurança (ADR-0009, ADR-0012)**

### **Logging e Auditoria**
- [ ] Operações sensíveis são auditadas pelo middleware de logging
- [ ] Não há vazamento de dados sensíveis nos logs
- [ ] Headers de segurança apropriados

### **Validação de Dados**
- [ ] Input validation em todas as mutations
- [ ] Sanitização de dados de entrada
- [ ] Tratamento adequado de erros

## 📝 **Documentação**

### **Código**
- [ ] Métodos complexos têm docblocks explicativos
- [ ] Constantes e enums são documentados
- [ ] README do módulo atualizado (se aplicável)

### **ADRs**
- [ ] Decisões arquiteturais significativas estão documentadas
- [ ] ADRs existentes foram consultadas e respeitadas
- [ ] Nova ADR criada se necessário

## 🚀 **Performance**

### **Banco de Dados**
- [ ] Consultas otimizadas (sem N+1 queries)
- [ ] Índices apropriados criados
- [ ] Uso eficiente de relacionamentos Eloquent

### **GraphQL**
- [ ] Queries não são excessivamente complexas
- [ ] Paginação implementada para listas grandes
- [ ] Uso adequado de DataLoader (se necessário)

## 🔧 **DevOps e Ambiente**

### **Docker**
- [ ] Comandos Docker executados no container correto
- [ ] Variáveis de ambiente configuradas apropriadamente
- [ ] Não há hardcoding de configurações

### **Dependências**
- [ ] Novas dependências justificadas e documentadas
- [ ] Versões específicas definidas em `composer.json`
- [ ] Compatibilidade com versões existentes

## ⚠️ **Validações Automatizadas**

### **Comandos de Verificação**

```bash
# Verificar migrations sem timestamps
grep -r "Schema::create" modules/*/Database/Migrations/ | xargs grep -L "timestamps()"

# Verificar models sem casts de datetime
grep -r "class.*extends Model" modules/ | while read file; do
    if ! grep -q "created_at.*datetime" "$file"; then
        echo "⚠️ Model sem cast de created_at: $file"
    fi
done

# Executar testes
cd ../realestate-infra && docker compose exec app php artisan test

# Verificar padrões de código
cd ../realestate-infra && docker compose exec app ./vendor/bin/pint --test
```

## 🚫 **Red Flags - Rejeição Automática**

### **Casos que resultam em rejeição imediata:**
- ❌ Migration cria tabela sem `timestamps()` (sem justificativa válida)
- ❌ Model não inclui casts para `created_at`/`updated_at`
- ❌ Implementação REST em vez de GraphQL
- ❌ Código não segue PSR-12
- ❌ Ausência de testes para funcionalidade crítica
- ❌ Vazamento de credenciais ou dados sensíveis
- ❌ Quebra de dependência modular
- ❌ Falta de header de copyright obrigatório

## ✅ **Aprovação Final**

### **Critérios para Merge:**
1. Todos os itens do checklist validados
2. Testes passando (100% de sucesso)
3. Padrões de código validados
4. Documentação atualizada
5. Revisão por pelo menos 1 developer senior
6. CI/CD pipeline verde

### **Responsabilidades:**
- **Autor do PR**: Garantir que todos os critérios são atendidos antes de abrir PR
- **Reviewer**: Validar cada item do checklist sistematicamente
- **Tech Lead**: Revisar decisões arquiteturais e impactos no sistema

## 📚 **Referências**
- [ADR-0002: Arquitetura Modular](doc/architectural-decision-records/0002-arquitetura-modular-com-evolucao-para-microservicos.md)
- [ADR-0003: GraphQL com Lighthouse](doc/architectural-decision-records/0003-graphql-com-lighthouse.md)
- [ADR-0006: Padrões de Código e PSR](doc/architectural-decision-records/0006-padroes-de-codigo-e-psr.md)
- [ADR-0011: Padronização e Isolamento de Testes](doc/architectural-decision-records/0011-padronizacao-e-isolamento-de-testes.md)
- [ADR-0013: Convenções de Banco de Dados](doc/architectural-decision-records/0013-convencoes-banco-dados-timestamps.md)

---
**Importante**: Este documento deve ser seguido rigorosamente. Exceções só são permitidas com justificativa técnica explícita e aprovação do Tech Lead.
