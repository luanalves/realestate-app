# ADR 0017: Implementação de Rate Limiting para Operações Sensíveis

## Status
Aceito

## Contexto
Em sistemas de alta segurança como uma aplicação de gestão imobiliária, operações sensíveis como reset de senha, login e outras ações críticas podem ser alvo de ataques de força bruta, DoS (Denial of Service) ou DDoS (Distributed Denial of Service). Sem limitação de taxa de requisições (rate limiting), um atacante pode realizar um grande número de tentativas em um curto período, potencialmente comprometendo a segurança e a disponibilidade do sistema.

Especificamente, o endpoint de recuperação de senha (`requestPasswordReset`) foi identificado como vulnerável, pois permitia número ilimitado de tentativas, facilitando:
- Ataques de enumeração de usuários
- Ataques de negação de serviço por sobrecarga do servidor de email
- Possibilidade de impacto na performance do servidor por excesso de requisições

## Decisão
Implementar rate limiting em operações sensíveis do sistema, começando pelo reset de senha e posteriormente estendendo para outras operações críticas como login, registro de usuário e alterações de dados importantes.

Decisões técnicas:
1. **Mecanismo de Rate Limiting**: Utilizar o `RateLimiter` nativo do Laravel para controlar o número de tentativas.
2. **Método de Identificação**: Combinar o endereço de e-mail e o endereço IP para criar chaves únicas de limitação, mitigando tanto ataques direcionados a usuários específicos quanto ataques distribuídos.
3. **Limites e Janelas de Tempo**: 
   - Reset de senha: Máximo de 5 tentativas por hora por combinação de email/IP
   - Login: Máximo de 10 tentativas por hora por combinação de usuário/IP (a ser implementado)
   - Registro: Máximo de 3 tentativas por hora por IP (a ser implementado)
4. **Feedback ao Usuário**: Fornecer mensagens claras indicando o tempo restante até que novas tentativas sejam permitidas.

### Implementação de Referência (Reset de Senha)
```php
// Criar chave de rate limiting combinando email e IP
$rateLimiterKey = Str::lower($email) . '|' . $ipAddress;

// Verificar se o limite foi excedido
if (RateLimiter::tooManyAttempts('password-reset:' . $rateLimiterKey, self::MAX_ATTEMPTS)) {
    $seconds = RateLimiter::availableIn('password-reset:' . $rateLimiterKey);
    $minutes = ceil($seconds / 60);
    
    return [
        'success' => false,
        'message' => "Too many password reset attempts. Please try again after {$minutes} " . 
                     ($minutes === 1 ? 'minute' : 'minutes') . '.',
    ];
}

// Incrementar o contador
RateLimiter::hit('password-reset:' . $rateLimiterKey, self::DECAY_MINUTES * 60);
```

## Consequências

### Positivas
1. **Maior segurança**: Redução significativa da eficácia de ataques de força bruta e enumeração de usuários
2. **Proteção contra DoS**: Mitigação de ataques visando sobrecarregar o sistema
3. **Melhor estabilidade**: Menor risco de degradação de performance devido a picos de requisições maliciosas
4. **Conformidade regulatória**: Alinhamento com melhores práticas de segurança como OWASP Top 10
5. **Proteção à reputação**: Evita que o domínio seja usado para enviar grande volume de emails, o que poderia impactar a reputação de entrega de emails

### Negativas
1. **Falsos positivos**: Usuários legítimos em redes compartilhadas (como escritórios corporativos) podem ser afetados por limites atingidos por outros usuários
2. **Complexidade adicional**: Necessidade de gerenciar e monitorar os limites de taxa
3. **Possível frustração do usuário**: Usuários legítimos podem ser impedidos de realizar operações em situações excepcionais

### Mitigações Planejadas
- Implementar monitoramento para detectar bloqueios excessivos que podem indicar falsos positivos
- Criar mecanismo de exceção para endereços IP confiáveis
- Considerar a implementação de verificação CAPTCHA como alternativa ao bloqueio total após exceder limites
