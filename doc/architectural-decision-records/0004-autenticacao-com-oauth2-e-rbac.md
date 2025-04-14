# ADR [0004]: Autenticacao Com Oauth2 E Rbac

**Status:** Aceito  
**Data:** 2025-04-03

## Contexto


A aplicação imobiliária será acessada por diferentes perfis de usuários (administradores da aplicação, funcionários das imobiliárias, corretores e clientes finais), cada um com permissões e jornadas distintas. Além disso, a interface principal da aplicação será baseada em GraphQL, consumida por um frontend desacoplado.

Nesse cenário, é necessário garantir:

- Segurança no acesso à API;
- Controle de sessão via tokens;
- Flexibilidade para autenticar múltiplas aplicações (por exemplo: painéis administrativos, apps mobile, etc.);
- Validação de permissões com base em perfis de usuários (RBAC).

## Decisão

Optou-se pela implementação da autenticação via **OAuth2 utilizando o Laravel Passport**.

Essa decisão é sustentada por dois fatores principais:

1. **Padrão de mercado seguro e consolidado**:  
   O OAuth2 é amplamente adotado por grandes plataformas como Google, GitHub, Spotify e outros. Ele oferece um modelo robusto para emissão de tokens de acesso e autorização baseada em escopo e tempo de expiração, sendo hoje o padrão mais seguro para APIs públicas e privadas.

2. **Integração nativa e simplificada com o Laravel**:  
   O pacote Laravel Passport é mantido pela própria equipe do Laravel, fornece uma implementação completa do OAuth2, e se integra naturalmente com os recursos de autenticação, middleware e controle de acesso da framework. Isso reduz significativamente a complexidade de implementação e manutenção da segurança da API.

Além da emissão de tokens com OAuth2, a aplicação também adota um segundo nível de controle baseado em **RBAC (Role-Based Access Control)**. Esse modelo permite associar permissões de acesso a perfis específicos, fortalecendo ainda mais a segurança e a clareza da autorização nas operações da API.

## Consequências

- As chamadas à API GraphQL exigirão um token válido do tipo Bearer (OAuth2).
- A autenticação inicial será realizada via `grant_type=password`, com clientes registrados no Passport.
- O frontend da aplicação armazenará e enviará esse token a cada requisição.
- A validação do perfil do usuário (RBAC) será feita nos resolvers, garantindo que apenas usuários autorizados possam executar certas operações.
- Novos perfis e escopos de acesso poderão ser adicionados com facilidade, mantendo a segurança e a escalabilidade do sistema.
