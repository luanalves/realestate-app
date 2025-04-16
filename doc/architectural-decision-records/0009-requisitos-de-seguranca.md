
# ADR [0009]: Requisitos de Segurança da Aplicação Imobiliária

**Status:** Aceito  
**Data:** 2025-04-13

## Contexto

A aplicação em desenvolvimento lida com dados sensíveis de clientes, usuários, leads, imóveis e operações comerciais, o que exige um conjunto de práticas e controles de segurança robustos. Além disso, trata-se de uma aplicação **multi-tenant** com potencial de expansão nacional, o que aumenta a superfície de ataque e demanda atenção especial à **privacidade dos dados**, **segurança na comunicação** e **proteção contra ataques comuns da web**.

Seguindo boas práticas de desenvolvimento seguro e conformidade com a **LGPD**, esta ADR define os requisitos mínimos de segurança para a primeira versão do sistema.

## Decisão

A arquitetura da aplicação adotará os seguintes requisitos e práticas de segurança:

### 🔐 Autenticação e Autorização
- Autenticação via **OAuth2 com JWT**, incluindo refresh token;
- Implementação de **controle de acesso por função (RBAC)**;
- Isolamento lógico de dados por Tenant (imobiliária) com validação obrigatória do `tenant_id` em todas as requisições;
- Suporte opcional a **MFA (autenticação em dois fatores)** para usuários administradores.

### 🛡️ Proteção Contra Ataques Web
- Proteção contra **SQL Injection**, **XSS**, **CSRF** e **Clickjacking**;
- Uso obrigatório de **prepared statements** no backend (ORM seguro como Prisma/TypeORM);
- Uso de **CSP (Content Security Policy)** e `X-Frame-Options` para reforçar a política de execução no front-end.

### 🔒 Proteção de Dados
- Criptografia de dados **em repouso** (AES-256) e **em trânsito** (TLS 1.2 ou superior);
- Hashing de senhas com **bcrypt**;
- Políticas de senha forte e expiração periódica de tokens;
- Possibilidade de exclusão ou anonimização de dados sensíveis conforme LGPD.

### 🧠 Segurança de APIs
- Todas as APIs serão protegidas por autenticação;
- Aplicação de **rate limiting** e logs para prevenir abuso de chamadas;
- Integração com WAF (ex.: Cloudflare ou AWS WAF) para proteger contra ataques automatizados e DDoS.

### 🧰 Monitoramento, Logs e Infraestrutura
- Registro de **logs de autenticação, acesso e falhas**;
- Integração com ferramentas como **Grafana, Prometheus e Loki** para observabilidade;
- Armazenamento seguro de credenciais e secrets usando **HashiCorp Vault ou AWS Secrets Manager**;
- Isolamento completo entre ambientes (dev, staging e produção) com controle de acesso.

### ♻️ Backups e Recuperação
- Backups automáticos criptografados, com testes periódicos de recuperação;
- Implementação de plano de continuidade com failover de serviços essenciais.

## Consequências

A aplicação passará a ter uma **base de segurança robusta desde o início**, protegendo usuários, empresas parceiras e dados internos.  
Essas medidas também **facilitam auditorias futuras**, aumentam a confiança do cliente e preparam o produto para escalar com segurança.

A equipe de desenvolvimento deverá considerar a **segurança como uma responsabilidade compartilhada**, adotando validações em todas as camadas da arquitetura (backend, frontend, infraestrutura).
