#!/bin/bash

# Validador de PR - Convenções de Banco de Dados e Qualidade de Código
# Executa validações obrigatórias conforme ADR-0013 e diretrizes de PR

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Contadores
ERRORS=0
WARNINGS=0

echo -e "${BLUE}🔍 Iniciando validação de PR...${NC}\n"

# Função para log de erro
log_error() {
    echo -e "${RED}❌ ERRO: $1${NC}"
    ((ERRORS++))
}

# Função para log de warning
log_warning() {
    echo -e "${YELLOW}⚠️  WARNING: $1${NC}"
    ((WARNINGS++))
}

# Função para log de sucesso
log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Função para log de info
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

echo -e "${BLUE}📋 1. Validando Convenções de Banco de Dados (ADR-0013)${NC}"

# 1.1 Verificar migrations sem timestamps
echo "🔍 Verificando migrations sem timestamps..."
MIGRATIONS_WITHOUT_TIMESTAMPS=$(find modules/*/Database/Migrations/ -name "*.php" -exec grep -l "Schema::create" {} \; | xargs grep -L "timestamps()" 2>/dev/null || true)

if [ ! -z "$MIGRATIONS_WITHOUT_TIMESTAMPS" ]; then
    log_error "Migrations sem timestamps() encontradas:"
    echo "$MIGRATIONS_WITHOUT_TIMESTAMPS" | while read file; do
        echo "   - $file"
    done
    echo "   Solução: Adicione \$table->timestamps(); nas migrations"
else
    log_success "Todas as migrations incluem timestamps()"
fi

# 1.2 Verificar models sem casts de datetime
echo "🔍 Verificando models sem casts de datetime..."
MODELS_WITHOUT_CASTS=$(find modules/*/Models/ -name "*.php" -exec grep -l "extends Model" {} \; | while read file; do
    if ! grep -q "created_at.*datetime" "$file" || ! grep -q "updated_at.*datetime" "$file"; then
        echo "$file"
    fi
done 2>/dev/null || true)

if [ ! -z "$MODELS_WITHOUT_CASTS" ]; then
    log_error "Models sem casts de datetime encontrados:"
    echo "$MODELS_WITHOUT_CASTS" | while read file; do
        echo "   - $file"
    done
    echo "   Solução: Adicione 'created_at' => 'datetime', 'updated_at' => 'datetime' no \$casts"
else
    log_success "Todos os models incluem casts de datetime"
fi

# 1.3 Verificar se há tabelas sendo criadas em migrations recentes
echo "🔍 Verificando migrations recentes..."
RECENT_MIGRATIONS=$(find modules/*/Database/Migrations/ -name "*.php" -newer modules/Security/Database/Migrations/.gitkeep 2>/dev/null || true)
if [ ! -z "$RECENT_MIGRATIONS" ]; then
    log_info "Migrations recentes encontradas - validação extra aplicada"
fi

echo -e "\n${BLUE}🏗️  2. Validando Estrutura de Código${NC}"

# 2.1 Verificar declare(strict_types=1)
echo "🔍 Verificando declare(strict_types=1)..."
FILES_WITHOUT_STRICT_TYPES=$(find modules/ -name "*.php" | xargs grep -L "declare(strict_types=1)" 2>/dev/null || true)

if [ ! -z "$FILES_WITHOUT_STRICT_TYPES" ]; then
    log_warning "Arquivos sem declare(strict_types=1) encontrados:"
    echo "$FILES_WITHOUT_STRICT_TYPES" | head -5 | while read file; do
        echo "   - $file"
    done
    if [ $(echo "$FILES_WITHOUT_STRICT_TYPES" | wc -l) -gt 5 ]; then
        echo "   ... e mais $(echo "$FILES_WITHOUT_STRICT_TYPES" | wc -l | awk '{print $1-5}') arquivos"
    fi
else
    log_success "Todos os arquivos incluem declare(strict_types=1)"
fi

# 2.2 Verificar header de copyright
echo "🔍 Verificando headers de copyright..."
FILES_WITHOUT_COPYRIGHT=$(find modules/ -name "*.php" | xargs grep -L "@author.*Luan Silva" 2>/dev/null || true)

if [ ! -z "$FILES_WITHOUT_COPYRIGHT" ]; then
    log_warning "Arquivos sem header de copyright encontrados:"
    echo "$FILES_WITHOUT_COPYRIGHT" | head -3 | while read file; do
        echo "   - $file"
    done
    if [ $(echo "$FILES_WITHOUT_COPYRIGHT" | wc -l) -gt 3 ]; then
        echo "   ... e mais arquivos"
    fi
else
    log_success "Todos os arquivos incluem header de copyright"
fi

echo -e "\n${BLUE}📐 3. Validando Padrões PSR-12${NC}"

# 3.1 Executar Pint (se disponível)
if [ -f "./vendor/bin/pint" ]; then
    echo "🔍 Executando verificação de padrões de código..."
    if command -v docker &> /dev/null && [ -f "../realestate-infra/docker-compose.yml" ]; then
        # Executar no Docker
        log_info "Executando Pint no container Docker..."
        if (cd ../realestate-infra && docker compose exec app ./vendor/bin/pint --test modules/ 2>/dev/null); then
            log_success "Padrões de código validados com sucesso"
        else
            log_error "Padrões de código não estão em conformidade. Execute: docker compose exec app ./vendor/bin/pint modules/"
        fi
    else
        # Executar localmente
        if ./vendor/bin/pint --test modules/ 2>/dev/null; then
            log_success "Padrões de código validados com sucesso"
        else
            log_error "Padrões de código não estão em conformidade. Execute: ./vendor/bin/pint modules/"
        fi
    fi
else
    log_warning "Pint não encontrado - pulando validação de padrões de código"
fi

echo -e "\n${BLUE}🧪 4. Validando Testes${NC}"

# 4.1 Verificar se há testes para novos arquivos
echo "🔍 Verificando cobertura de testes..."
RESOLVER_FILES=$(find modules/*/GraphQL/ -name "*.php" 2>/dev/null || true)
if [ ! -z "$RESOLVER_FILES" ]; then
    TEST_FILES=$(find tests/Feature/ -name "*Test.php" 2>/dev/null | wc -l)
    if [ $TEST_FILES -eq 0 ]; then
        log_warning "Nenhum arquivo de teste encontrado em tests/Feature/"
    else
        log_success "Arquivos de teste encontrados ($TEST_FILES arquivos)"
    fi
fi

# 4.2 Executar testes (se solicitado)
if [ "$1" = "--run-tests" ]; then
    echo "🔍 Executando testes..."
    if command -v docker &> /dev/null && [ -f "../realestate-infra/docker-compose.yml" ]; then
        log_info "Executando testes no container Docker..."
        if (cd ../realestate-infra && docker compose exec app php artisan test); then
            log_success "Todos os testes passaram"
        else
            log_error "Alguns testes falharam"
        fi
    else
        log_warning "Docker não disponível - pule a execução de testes manuais"
    fi
fi

echo -e "\n${BLUE}📊 5. Validando GraphQL${NC}"

# 5.1 Verificar arquivos schema.graphql
echo "🔍 Verificando schemas GraphQL..."
SCHEMA_FILES=$(find modules/*/GraphQL/ -name "schema.graphql" 2>/dev/null || true)
if [ ! -z "$SCHEMA_FILES" ]; then
    log_success "Schemas GraphQL encontrados ($(echo "$SCHEMA_FILES" | wc -l) arquivos)"
else
    log_info "Nenhum schema GraphQL novo encontrado"
fi

# 5.2 Verificar se há controllers REST (não deveria haver)
REST_CONTROLLERS=$(find modules/*/Http/Controllers/ -name "*Controller.php" 2>/dev/null | xargs grep -l "Route::" 2>/dev/null || true)
if [ ! -z "$REST_CONTROLLERS" ]; then
    log_warning "Possíveis controllers REST encontrados (deveria usar GraphQL):"
    echo "$REST_CONTROLLERS" | while read file; do
        echo "   - $file"
    done
fi

echo -e "\n${BLUE}📋 6. Resumo da Validação${NC}"

if [ $ERRORS -eq 0 ]; then
    log_success "✨ Nenhum erro crítico encontrado!"
else
    log_error "🚫 $ERRORS erro(s) crítico(s) encontrado(s) - PR não pode ser aprovado"
fi

if [ $WARNINGS -gt 0 ]; then
    log_warning "⚠️  $WARNINGS warning(s) encontrado(s) - revisar antes do merge"
fi

echo -e "\n${BLUE}🛠️  Comandos para Correção:${NC}"
echo "# Para corrigir padrões de código:"
echo "cd ../realestate-infra && docker compose exec app ./vendor/bin/pint modules/"
echo ""
echo "# Para executar testes:"
echo "cd ../realestate-infra && docker compose exec app php artisan test"
echo ""
echo "# Para verificar migrations sem timestamps:"
echo "grep -r \"Schema::create\" modules/*/Database/Migrations/ | xargs grep -L \"timestamps()\""

echo -e "\n${BLUE}📚 Referências:${NC}"
echo "- ADR-0013: doc/architectural-decision-records/0013-convencoes-banco-dados-timestamps.md"
echo "- Diretrizes de PR: .github/pull-request-guidelines.md"

# Código de saída
if [ $ERRORS -gt 0 ]; then
    exit 1
else
    exit 0
fi
