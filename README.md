# RealEstate App

Sistema de gestão imobiliária desenvolvido com foco em performance, escalabilidade e modularidade.
O projeto segue princípios de arquitetura limpa, DDD e modularização baseada em domínio.

---

## 🧱 Características da Arquitetura

- 🔧 **Laravel 12** como framework principal
- 🧩 **Arquitetura modular por domínio** (UserManagement, RealEstate, Leads, etc)
- 📡 **GraphQL com Lighthouse** para APIs flexíveis
- 🗄️ **PostgreSQL, Redis, MongoDB** como suporte a diferentes tipos de persistência
- ✉️ **Mensageria (Kafka ou RabbitMQ)** em planejamento futuro
- 📁 Cada módulo possui seus próprios:
  - Controllers
  - Models
  - Providers
  - Migrations
  - Seeders
  - GraphQL Schemas

---

Aplicação backend construída com Laravel 12 utilizando arquitetura modular, GraphQL e suporte a múltiplos bancos (PostgreSQL, MongoDB e Redis).

---

## 📁 Estrutura do Projeto

```
realestate-app/
├── app/
├── bootstrap/
├── config/
├── database/
├── modules/
│   └── UserManagement/
│       ├── Http/
│       ├── Models/
│       ├── Providers/
│       ├── GraphQL/
│       └── Database/
│           ├── Migrations/
│           └── Seeders/
├── routes/
├── public/
└── ...
```

---

## 📦 Módulos

Os módulos seguem uma estrutura isolada com seus próprios:

- Controllers
- Models
- Providers
- GraphQL Schemas
- Migrations & Seeders

---

## ⚙️ Configurações obrigatórias

### ✅ Providers

Cada módulo deve registrar seu ServiceProvider no arquivo:

```php
// bootstrap/providers.php

return [
    ...
    Modules\UserManagement\Providers\UserManagementServiceProvider::class,
];
```

### ✅ Migrations

Para que o Laravel reconheça as migrations de dentro dos módulos, você deve registrá-las em:

```php
// app/Providers/AppServiceProvider.php

public function boot(): void
{
    $this->loadMigrationsFrom(base_path('modules/UserManagement/Database/Migrations'));
}
```


## 🧬 Seeders por módulo

Cada módulo possui seu próprio seeder principal localizado em:

```
modules/NomeDoModulo/Database/Seeders/DatabaseSeeder.php
```

Para ativar os seeders do módulo, registre-o no `DatabaseSeeder` principal da aplicação localizado em `database/seeders/DatabaseSeeder.php`:

```php
$this->call(\Modules\UserManagement\Database\Seeders\DatabaseSeeder::class);
```

Isso garante que, ao executar:

```bash
php artisan db:seed
```

Todos os dados daquele módulo sejam populados corretamente.

### ✅ GraphQL (Lighthouse)

- O Lighthouse é usado para expor a API via GraphQL
- Configuração do caminho do schema está em `config/lighthouse.php`:

```php
'schema_path' => base_path('modules/UserManagement/GraphQL/schema.graphql'),
```

- A rota `/graphql` é utilizada para envio das queries e mutations

---

## 🧪 Testes e Uso

- Para testar a mutation de criação de usuários, utilize Postman, Insomnia ou Altair
- Endpoint: `http://localhost:8080/graphql`

## 🚧 Em desenvolvimento

- Implementação de autenticação
- Integração com mais módulos (Imóveis, Leads, Contratos, etc.)
## ✅ Checklist para criação de um novo módulo

- [ ] Criar diretório `modules/NomeModulo`
- [ ] Criar Provider e registrar em `bootstrap/providers.php`
- [ ] Criar migrations e registrar via `AppServiceProvider`
- [ ] Criar GraphQL Schema em `modules/NomeModulo/GraphQL/schema.graphql`
- [ ] Atualizar `config/lighthouse.php` com o caminho do schema, se necessário
- [ ] Criar Controller/Resolver e Request (FormRequest) para validações
- [ ] Criar Seeders se houver dados base (ex: perfis, categorias, etc)
- [ ] Atualizar README.md com a descrição do novo módulo

---

// ...existing code...
- [ ] Atualizar README.md com a descrição do novo módulo

---

## 🚀 Processo de Soft Launch

### 🔐 Chaves de Segurança OAuth

As chaves OAuth do Laravel Passport (`oauth-private.key` e `oauth-public.key`) devem ser tratadas com segurança:

- **NÃO comite estes arquivos no controle de versão**
- Adicione-os ao `.gitignore`:
  ```
  /storage/oauth-*.key
  ```
- Gere as chaves durante a implantação:
  ```bash
  cd ../realestate-infra && docker compose exec app php artisan passport:keys
  ```
- Configure permissões adequadas em produção:
  ```bash
  cd ../realestate-infra && docker compose exec app chmod 600 storage/oauth-private.key
  ```
- Durante o pipeline de CI/CD, garanta que as chaves sejam geradas como parte do processo de implantação
- Para desenvolvimento local, execute o comando de geração de chaves após a configuração inicial

Estas chaves são componentes críticos de segurança que assinam e verificam tokens de autenticação para sua API. Chaves comprometidas podem permitir acesso não autorizado à API.

---