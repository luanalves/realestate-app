# Módulo BFFAuth

Este módulo é responsável pela autenticação centralizada (BFF) da aplicação headless, protegendo o client_secret e gerenciando tokens de acesso para os frontends.

## Endpoints
- POST /bffauth/login
- POST /bffauth/refresh
- POST /bffauth/logout

## Estrutura
- Http/Controllers: Controllers dos endpoints
- Http/Requests: Validação de requests
- Services: Lógica de requisição de token e revogação
- Providers: ServiceProvider do módulo
- routes: Definição das rotas
- Tests/Feature: Testes automatizados

## Observações
- Nunca exponha o client_secret ao frontend
- Sempre consulte as ADRs do projeto para garantir aderência ao padrão
