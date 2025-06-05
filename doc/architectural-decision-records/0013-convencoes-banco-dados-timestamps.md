# ADR [0013]: Convenções de Banco de Dados para Auditoria e Rastreamento

## Status
Aceito

## Contexto
Em sistemas empresariais, especialmente em aplicações imobiliárias que lidam com transações financeiras e dados sensíveis, é fundamental manter rastreabilidade temporal de todas as operações. A padronização de convenções de banco de dados garante consistência, facilita auditorias e permite análises temporais eficientes.

Atualmente, o projeto possui diferentes abordagens para campos de timestamp em suas tabelas, o que pode causar inconsistências na auditoria e dificuldades na análise de dados históricos.

## Decisão
Todas as tabelas do sistema **DEVEM** seguir as seguintes convenções obrigatórias:

### 1. **Campos de Timestamp Obrigatórios**
- **`created_at`**: Campo do tipo `TIMESTAMP` que registra o momento da criação do registro
- **`updated_at`**: Campo do tipo `TIMESTAMP` que registra o momento da última atualização

### 2. **Implementação em Migrations**
```php
// ✅ OBRIGATÓRIO em todas as migrations
$table->timestamps();

// ✅ Alternativa explícita (se necessário controle específico)
$table->timestamp('created_at')->nullable();
$table->timestamp('updated_at')->nullable();
```

### 3. **Implementação em Models**
```php
// ✅ OBRIGATÓRIO em todos os models Eloquent
protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

// ✅ Para campos adicionais de data/hora
protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime', // Para soft deletes
    'verified_at' => 'datetime', // Para timestamps específicos
];
```

### 4. **Exceções Permitidas**
- **Tabelas de lookup/referência**: Tabelas estáticas com dados que raramente mudam podem omitir `updated_at`
- **Tabelas de log específicas**: Quando `updated_at` não faz sentido no contexto (ex: logs imutáveis)
- **Tabelas pivô simples**: Quando não há necessidade de rastreamento temporal

### 5. **Índices Recomendados**
```php
// ✅ Para consultas temporais otimizadas
$table->index(['created_at']);
$table->index(['updated_at']);

// ✅ Para consultas compostas frequentes
$table->index(['user_id', 'created_at']);
$table->index(['status', 'created_at']);
```

## Implementação

### **Revisão de Código**
- Todas as migrations **DEVEM** ser revisadas para garantir a presença de `timestamps()`
- Models **DEVEM** incluir cast apropriado para campos de data/hora
- PRs que criem/modifiquem tabelas **DEVEM** seguir estas convenções

### **Migração de Tabelas Existentes**
```php
// Para tabelas existentes sem timestamps
Schema::table('nome_da_tabela', function (Blueprint $table) {
    $table->timestamps();
});
```

### **Auditoria e Monitoramento**
- Ferramentas de CI/CD devem validar a presença de timestamps em novas migrations
- Relatórios de auditoria devem utilizar `created_at` como referência temporal padrão
- Análises de performance devem considerar índices temporais

## Consequências

### **Vantagens**
- ✅ **Rastreabilidade**: Capacidade de rastrear quando cada registro foi criado/modificado
- ✅ **Auditoria**: Facilita auditorias e conformidade regulatória
- ✅ **Análise Temporal**: Permite análises de tendências e padrões temporais
- ✅ **Debugging**: Facilita investigação de problemas e inconsistências
- ✅ **Backup/Restore**: Estratégias de backup baseadas em timestamps
- ✅ **Performance**: Índices temporais otimizam consultas por período

### **Desvantagens**
- ❌ **Espaço**: Overhead de 16 bytes por registro (8 bytes × 2 campos)
- ❌ **Complexidade**: Necessidade de manutenção de índices adicionais
- ❌ **Migração**: Trabalho para adequar tabelas existentes

### **Riscos Mitigados**
- 🛡️ **Perda de rastreabilidade**: Impossibilidade de determinar quando dados foram alterados
- 🛡️ **Problemas de auditoria**: Dificuldade em atender requisitos regulatórios
- 🛡️ **Análise limitada**: Incapacidade de gerar relatórios temporais precisos
- 🛡️ **Debugging complexo**: Dificuldade em rastrear origem de problemas

## Exemplos

### **Migration Correta**
```php
public function up(): void
{
    Schema::create('properties', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->decimal('price', 15, 2);
        $table->unsignedBigInteger('user_id');
        $table->timestamps(); // ✅ OBRIGATÓRIO
        
        $table->index(['user_id', 'created_at']); // ✅ RECOMENDADO
    });
}
```

### **Model Correto**
```php
class Property extends Model
{
    protected $fillable = ['title', 'price', 'user_id'];
    
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime', // ✅ OBRIGATÓRIO
        'updated_at' => 'datetime', // ✅ OBRIGATÓRIO
    ];
}
```

## Ferramentas de Validação

### **Checklist para Code Review**
- [ ] Migration inclui `$table->timestamps()`
- [ ] Model inclui casts para `created_at` e `updated_at`
- [ ] Índices apropriados foram criados
- [ ] Documentação foi atualizada se necessário

### **Comando de Verificação**
```bash
# Verificar migrations sem timestamps
grep -r "Schema::create" modules/*/Database/Migrations/ | xargs grep -L "timestamps()"
```

## Relacionados
- ADR-0005: Estrutura de Módulos com Seed e Migrations Isoladas
- ADR-0012: Auditoria Híbrida de Ações GraphQL com PostgreSQL + MongoDB
