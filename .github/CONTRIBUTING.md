# Contributing Guidelines

Bem-vindo! Para contribuir com este projeto, siga as diretrizes abaixo:

## Checklist de Revisão
- [ ] Seguir as decisões arquiteturais das ADRs (`doc/architectural-decision-records`)
- [ ] Seguir PSR-1, PSR-12 e boas práticas PHP
- [ ] Utilizar validação de requests do Laravel
- [ ] Utilizar Service Providers para registro de módulos
- [ ] Utilizar Dependency Injection sempre que possível
- [ ] Garantir que migrations e seeders estejam corretos e versionados
- [ ] Escrever testes para novas funcionalidades
- [ ] Documentar código complexo

## Boas Práticas de Desenvolvimento PHP
- Siga as recomendações das PSRs:
  - [PSR-1: Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)
  - [PSR-12: Extended Coding Style Guide](https://www.php-fig.org/psr/psr-12/)
- Utilize tipagem estrita (`declare(strict_types=1);` no topo dos arquivos PHP)
- Comente trechos de lógica complexa
- Prefira nomes descritivos para variáveis, métodos e classes
- Utilize constantes para valores fixos e reutilizáveis

## Recomendações para Laravel
- Utilize Form Requests para validação de dados
- Registre módulos e serviços via Service Providers
- Prefira Dependency Injection em controllers, services e resolvers
- Mantenha migrations e seeders organizados por módulo
- Utilize seeders para dados iniciais e constantes
- Siga a arquitetura modular definida nas ADRs

## Testes
- Escreva testes para toda nova funcionalidade (unitários e de integração)
- Utilize mocks para isolar dependências
- Siga a estrutura de testes definida no projeto
- Execute os testes no container Docker conforme instruções do README

## Como criar Pull Requests
- Descreva claramente o objetivo da PR
- Relacione a PR a uma issue existente, se aplicável
- Siga o checklist de revisão antes de submeter
- Aguarde revisão de pelo menos um mantenedor
- Corrija eventuais conflitos antes do merge

## Como criar Issues
- Descreva o problema ou sugestão de forma clara e objetiva
- Inclua contexto, prints, logs ou exemplos se possível
- Relacione a issue a uma funcionalidade ou módulo

## Links Úteis
- [ADRs - Decisões Arquiteturais](../doc/architectural-decision-records)
- [Documentação Laravel](https://laravel.com/docs)
- [PSR-1](https://www.php-fig.org/psr/psr-1/)
- [PSR-12](https://www.php-fig.org/psr/psr-12/)
- [Código de Conduta](./CODE_OF_CONDUCT.md)

## Observações
- Consulte sempre as ADRs antes de propor mudanças estruturais
- Mantenha o padrão de modularização e versionamento
- Dúvidas? Abra uma issue ou consulte os mantenedores
