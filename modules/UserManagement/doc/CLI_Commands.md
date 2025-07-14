# UserManagement CLI Commands

Documentação completa dos comandos de linha de comando disponíveis no módulo UserManagement.

---

## 📋 Visão Geral

O módulo UserManagement disponibiliza **3 comandos principais** para operações administrativas:

1. **`user:cache`** - Gestão e análise do sistema de cache Redis
2. **`oauth:tokens`** - Análise e gestão de tokens OAuth
3. **`user:reset-password`** - Reset de senhas de usuários

Todos os comandos devem ser executados **dentro do container Docker**.

---

## 🔧 Comando Base

Todos os comandos devem usar o padrão:

```bash
cd ../realestate-infra && docker compose exec app php artisan COMANDO
```

---

## 1. 🗄️ Cache Management: `user:cache`

Gerencia e analisa o sistema de cache Redis para usuários.

### **Sintaxe**
```bash
user:cache {action} [--user-id=] [--detailed]
```

### **Ações Disponíveis**

#### **📊 `info` - Informações do Cache**
```bash
cd ../realestate-infra && docker compose exec app php artisan user:cache info
```

**Saída:**
```
🗄️ User Cache Information

📈 Cache Statistics:
┌─────────────────────┬─────────┐
│ Metric              │ Value   │
├─────────────────────┼─────────┤
│ Cache Driver        │ redis   │
│ Redis Connection    │ ✅ OK    │
│ Total Cached Users  │ 15      │
│ Cache Hit Rate      │ 94.2%   │
│ Average TTL         │ 847s    │
└─────────────────────┴─────────┘

🔑 Sample Cache Keys:
• user_management:id:1 (TTL: 892s)
• user_management:id:2 (TTL: 445s)
• user_management:id:3 (TTL: 234s)
```

#### **🧹 `clear` - Limpar Cache**
```bash
# Limpar cache de todos os usuários
cd ../realestate-infra && docker compose exec app php artisan user:cache clear

# Limpar cache de usuário específico
cd ../realestate-infra && docker compose exec app php artisan user:cache clear --user-id=1
```

#### **🔄 `warm` - Pré-carregar Cache**
```bash
# Pré-carregar cache de todos os usuários
cd ../realestate-infra && docker compose exec app php artisan user:cache warm

# Pré-carregar cache de usuário específico
cd ../realestate-infra && docker compose exec app php artisan user:cache warm --user-id=1
```

#### **📋 `list` - Listar Entradas do Cache**
```bash
# Listar todas as entradas
cd ../realestate-infra && docker compose exec app php artisan user:cache list

# Versão detalhada
cd ../realestate-infra && docker compose exec app php artisan user:cache list --detailed
```

### **Opções**
- `--user-id=ID` - Filtrar por ID específico do usuário
- `--detailed` - Mostrar informações detalhadas

### **Exemplo de Uso Completo**
```bash
# 1. Verificar status do cache
cd ../realestate-infra && docker compose exec app php artisan user:cache info

# 2. Limpar cache específico
cd ../realestate-infra && docker compose exec app php artisan user:cache clear --user-id=5

# 3. Pré-carregar cache
cd ../realestate-infra && docker compose exec app php artisan user:cache warm --user-id=5

# 4. Verificar resultado
cd ../realestate-infra && docker compose exec app php artisan user:cache list --user-id=5 --detailed
```

---

## 2. 🔑 OAuth Token Analysis: `oauth:tokens`

Analisa e gerencia tokens OAuth do Laravel Passport.

### **Sintaxe**
```bash
oauth:tokens {action} [--user-id=] [--show-expired]
```

### **Ações Disponíveis**

#### **📋 `list` - Listar Tokens**
```bash
# Listar todos os tokens ativos
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens list

# Incluir tokens expirados
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens list --show-expired

# Filtrar por usuário
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens list --user-id=1
```

**Saída:**
```
🔑 OAuth Access Tokens Analysis

┌─────────────┬─────────┬─────────────┬──────────────────────┬─────────────────────┬─────────────────────┬─────────────────┬──────────┐
│ Token ID    │ User ID │ Name        │ Email                │ Created             │ Expires             │ Time to Expire  │ Status   │
├─────────────┼─────────┼─────────────┼──────────────────────┼─────────────────────┼─────────────────────┼─────────────────┼──────────┤
│ eyJ0eXAi... │ 1       │ John Doe    │ john@example.com     │ 2025-06-30 10:00:00 │ 2025-07-30 10:00:00 │ 29 days         │ 🟢 Active │
│ def50200... │ 2       │ Jane Smith  │ jane@example.com     │ 2025-06-30 09:30:00 │ 2025-07-30 09:30:00 │ 29 days         │ 🟢 Active │
└─────────────┴─────────┴─────────────┴──────────────────────┴─────────────────────┴─────────────────────┴─────────────────┴──────────┘

📊 Total tokens found: 2
```

#### **📈 `analyze` - Análise Detalhada**
```bash
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens analyze
```

**Saída:**
```
📈 OAuth Token Behavior Analysis

┌─────────────────┬───────┬────────────┐
│ Metric          │ Count │ Percentage │
├─────────────────┼───────┼────────────┤
│ Total Tokens    │ 25    │ 100%       │
│ Active Tokens   │ 18    │ 72.0%      │
│ Expired Tokens  │ 5     │ 20.0%      │
│ Revoked Tokens  │ 2     │ 8.0%       │
└─────────────────┴───────┴────────────┘

⏰ Token Expiration Configuration
🕐 Token Lifetime: 365 days
📅 Sample Token Created: 2025-06-30 10:00:00
📅 Sample Token Expires: 2026-06-30 10:00:00

👥 User Token Distribution
┌──────────────────────┬──────────────┐
│ User Email           │ Total Tokens │
├──────────────────────┼──────────────┤
│ john@example.com     │ 8           │
│ admin@example.com    │ 6           │
│ member@example.com   │ 4           │
└──────────────────────┴──────────────┘

💡 Key Insights:
• Each login generates a NEW token (no session reuse)
• Multiple active tokens per user are normal
• Tokens expire automatically based on configuration
• Old tokens should be cleaned up periodically
```

#### **🧹 `cleanup` - Limpeza de Tokens**
```bash
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens cleanup
```

### **Opções**
- `--user-id=ID` - Filtrar por usuário específico
- `--show-expired` - Incluir tokens expirados na listagem

---

## 3. 🔐 Password Reset: `user:reset-password`

Reset de senhas de usuários via linha de comando.

### **Sintaxe**
```bash
user:reset-password {email} [--password=] [--show-password]
```

### **Uso Básico**

#### **🔄 Reset com Senha Personalizada**
```bash
cd ../realestate-infra && docker compose exec app php artisan user:reset-password member@example.com --password=novaSenha123 --show-password
```

#### **🎲 Gerar Senha Automaticamente**
```bash
cd ../realestate-infra && docker compose exec app php artisan user:reset-password member@example.com --show-password
```

### **Saída Exemplo**
```
🔐 User Password Reset Tool

👤 User found:
   Name: Demo Member
   Email: member@example.com
   Current Role: client

 Do you want to reset the password for this user? (yes/no) [no]:
 > yes

✅ Password successfully reset!

🔑 New password: novaSenha123

⚠️  Please save this password securely and share it with the user through a secure channel.

🛡️  Security recommendations:
   • User should change this password on first login
   • Consider implementing password expiration policies
   • Monitor login attempts for this user
```

### **Funcionalidades**

#### **🔍 Validação de Usuário**
Se o email não for encontrado, o comando mostra usuários disponíveis:

```bash
cd ../realestate-infra && docker compose exec app php artisan user:reset-password email-inexistente@test.com
```

```
❌ User with email 'email-inexistente@test.com' not found.

💡 Available users:
┌────┬─────────────────────┬──────────────────────┬────────────────┐
│ ID │ Name                │ Email                │ Role           │
├────┼─────────────────────┼──────────────────────┼────────────────┤
│ 1  │ Luan Silva          │ contato@thedev...    │ super_admin    │
│ 2  │ Admin User          │ admin@example.com    │ admin          │
│ 3  │ Demo Member         │ member@example.com   │ client         │
└────┴─────────────────────┴──────────────────────┴────────────────┘
```

#### **🛡️ Validação de Senha**
O comando valida força da senha automaticamente:

- ✅ Mínimo 8 caracteres
- ✅ Pelo menos 1 letra minúscula
- ✅ Pelo menos 1 letra maiúscula  
- ✅ Pelo menos 1 número

#### **🎲 Geração Automática**
Quando não especificada, a senha é gerada automaticamente com:

- 12 caracteres de comprimento
- Mistura balanceada de maiúsculas, minúsculas, números e símbolos
- Garantia de pelo menos 1 caractere de cada tipo
- Embaralhamento aleatório final

### **Opções**
- `--password=SENHA` - Especificar senha customizada
- `--show-password` - Exibir a senha na saída (recomendado)

### **Casos de Uso**

#### **🚨 Emergência - Reset Rápido**
```bash
cd ../realestate-infra && docker compose exec app php artisan user:reset-password admin@example.com --password=TempPass123! --show-password
```

#### **👥 Novo Usuário - Senha Inicial**
```bash
cd ../realestate-infra && docker compose exec app php artisan user:reset-password novousuario@empresa.com --show-password
```

#### **🔒 Usuário Bloqueado - Desbloqueio**
```bash
cd ../realestate-infra && docker compose exec app php artisan user:reset-password usuario@bloqueado.com --password=NovaSegura456 --show-password
```

---

## 🔧 Comandos de Diagnóstico

### **Verificar Status Geral**
```bash
# Status do cache
cd ../realestate-infra && docker compose exec app php artisan user:cache info

# Status dos tokens
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens analyze

# Listar usuários disponíveis
cd ../realestate-infra && docker compose exec app php artisan user:reset-password email-falso@test.com
```

### **Limpeza de Manutenção**
```bash
# Limpar cache antigo
cd ../realestate-infra && docker compose exec app php artisan user:cache clear

# Limpar tokens expirados
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens cleanup

# Recarregar cache
cd ../realestate-infra && docker compose exec app php artisan user:cache warm
```

---

## 🚨 Troubleshooting

### **Problemas Comuns**

#### **❌ Redis não conecta**
```bash
# Verificar status do Redis
cd ../realestate-infra && docker compose exec app php artisan user:cache info

# Se falhar, verificar configuração
cd ../realestate-infra && docker compose exec app php artisan config:show cache
```

#### **❌ Tokens não aparecem**
```bash
# Verificar se há tokens no banco
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens list --show-expired

# Se vazio, criar token de teste
cd ../realestate-infra && docker compose exec app php artisan passport:client --password
```

#### **❌ Usuário não encontrado**
```bash
# Listar todos os usuários
cd ../realestate-infra && docker compose exec app php artisan user:reset-password email-qualquer@test.com

# Se lista vazia, executar seeders
cd ../realestate-infra && docker compose exec app php artisan db:seed
```

### **Logs de Debug**

Todos os comandos geram logs detalhados. Para verificar:

```bash
# Logs da aplicação
cd ../realestate-infra && docker compose exec app tail -f storage/logs/laravel.log

# Logs específicos de cache
cd ../realestate-infra && docker compose exec app php artisan user:cache info --detailed
```

---

## 📊 Exemplos de Workflows

### **🔄 Workflow de Manutenção Semanal**
```bash
# 1. Analisar tokens OAuth
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens analyze

# 2. Limpar tokens expirados
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens cleanup

# 3. Verificar status do cache
cd ../realestate-infra && docker compose exec app php artisan user:cache info

# 4. Limpar cache antigo
cd ../realestate-infra && docker compose exec app php artisan user:cache clear

# 5. Pré-carregar cache
cd ../realestate-infra && docker compose exec app php artisan user:cache warm
```

### **🚨 Workflow de Incidente de Segurança**
```bash
# 1. Listar todos os tokens ativos
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens list

# 2. Resetar senha do usuário comprometido
cd ../realestate-infra && docker compose exec app php artisan user:reset-password usuario@comprometido.com --show-password

# 3. Limpar cache do usuário
cd ../realestate-infra && docker compose exec app php artisan user:cache clear --user-id=ID_USUARIO

# 4. Analisar padrões de tokens
cd ../realestate-infra && docker compose exec app php artisan oauth:tokens analyze
```

### **👥 Workflow de Onboarding**
```bash
# 1. Verificar se usuário já existe
cd ../realestate-infra && docker compose exec app php artisan user:reset-password novousuario@empresa.com

# 2. Se existir, gerar nova senha
cd ../realestate-infra && docker compose exec app php artisan user:reset-password novousuario@empresa.com --show-password

# 3. Pré-carregar dados no cache
cd ../realestate-infra && docker compose exec app php artisan user:cache warm --user-id=ID_NOVO_USUARIO
```

---

**💡 Dica**: Use sempre o flag `--show-password` ao resetar senhas para ter certeza de que a operação foi bem-sucedida e poder comunicar a nova senha ao usuário de forma segura.
