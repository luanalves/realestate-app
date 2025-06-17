# ADR [0006]: Padroes De Codigo E Psr

**Status:** Aceito  
**Data:** 2025-04-04

# ADR [0006]: Padrões de Código, PSR e Princípios SOLID

**Status:** Aceito  
**Data:** 2025-04-04  
**Última atualização:** 2025-06-17

## Contexto

Esta decisão foi tomada durante a fase inicial de arquitetura do projeto de software imobiliário, levando em consideração requisitos técnicos, experiência da equipe e capacidade de escalabilidade.

Durante o desenvolvimento, identificamos a necessidade de estabelecer padrões mais rigorosos para garantir a qualidade, manutenibilidade e consistência do código.

## Decisão

### Padrões PSR
Seguiremos as seguintes PSRs (PHP Standards Recommendations):
- **PSR-1:** Basic Coding Standard
- **PSR-4:** Autoloader
- **PSR-12:** Extended Coding Style Guide

### Ferramentas de Qualidade de Código
- **Laravel Pint:** Para formatação automática do código
- **PHP-CS-Fixer:** Para correções adicionais de estilo

### Princípios SOLID
A aplicação deve seguir rigorosamente os princípios SOLID:

#### Single Responsibility Principle (SRP) - **OBRIGATÓRIO**
- Cada classe deve ter **uma única responsabilidade**
- Evitar classes "God" que fazem muitas coisas
- **Exemplo:** Criar serviços específicos para autorização (`SecurityAuthorizationService`, `UserManagementAuthorizationService`) em vez de colocar lógica de autenticação diretamente nos resolvers

#### Open/Closed Principle (OCP)
- Classes abertas para extensão, fechadas para modificação
- Usar interfaces e abstrações quando apropriado

#### Liskov Substitution Principle (LSP)
- Subclasses devem ser substituíveis por suas classes base
- Manter contratos consistentes em hierarquias

#### Interface Segregation Principle (ISP)
- Interfaces pequenas e específicas
- Evitar interfaces "fat" com muitos métodos

#### Dependency Inversion Principle (DIP)
- Depender de abstrações, não de implementações concretas
- Usar injeção de dependência

### Padrões de Desenvolvimento
- **Service Layer Pattern:** Lógica de negócio isolada em serviços
- **Authorization Service Pattern:** Centralização de lógica de autenticação/autorização
- **Repository Pattern:** Quando aplicável para abstração de dados
- **DRY Principle:** Don't Repeat Yourself - eliminar duplicação de código

## Consequências

### Positivas
- **Manutenibilidade:** Código mais fácil de manter e evoluir
- **Testabilidade:** Classes com responsabilidade única são mais fáceis de testar
- **Reutilização:** Componentes bem definidos podem ser reutilizados
- **Qualidade:** Padrões consistentes melhoram a qualidade geral
- **Colaboração:** Equipe trabalha com padrões comuns

### Responsabilidades
- **Desenvolvedores:** Devem seguir os princípios em todas as implementações
- **Code Review:** Verificar aderência aos padrões durante revisões
- **Refatoração:** Identificar e corrigir violações dos princípios
- **Documentação:** Manter padrões documentados em `doc/patterns/`

### Exemplo de Implementação
```php
// ❌ ANTES - Violação do SRP
class SecurityLogStats {
    public function __invoke() {
        // Autenticação + Autorização + Lógica de negócio tudo misturado
        if (!Auth::guard('api')->check()) { /* ... */ }
        if (!in_array($user->role->name, ['admin'])) { /* ... */ }
        return $this->getStatistics(); // Lógica de negócio
    }
}

// ✅ DEPOIS - Seguindo SRP
class SecurityLogStats {
    private SecurityAuthorizationService $authService; // SRP: Só autorização
    private SecurityLogService $logService; // SRP: Só lógica de negócio
    
    public function __invoke() {
        $this->authService->authorizeSecurityLogAccess(); // Responsabilidade delegada
        return $this->logService->getStatistics(); // Responsabilidade delegada
    }
}
```

A aplicação deverá seguir essa diretriz daqui para frente. Todas as novas implementações e decisões relacionadas devem considerar essa escolha como base.
