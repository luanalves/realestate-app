# ADR [0005]: Estrutura De Modulos Com Seed E Migrations Isoladas

**Status:** Aceito  
**Data:** 2025-04-04

## Contexto

Esta ADR complementa a decisão registrada na [ADR 0002 - Arquitetura Modular com Evolução para Microserviços](0002-arquitetura-modular-com-evolucao-para-microservicos.md), onde foi definido que a aplicação seria construída com base em módulos por domínio de negócio.

Com o avanço da implementação, ficou claro que cada módulo não deve apenas conter suas rotas, resolvers e schemas, mas também **ser responsável por sua própria estrutura de banco de dados e dados de inicialização (seeds)**.

A organização tradicional centralizada de migrations e seeders no diretório `database/` da aplicação principal acaba dificultando o isolamento, reuso e escalabilidade dos módulos.

## Decisão

Cada módulo da aplicação (ex: `UserManagement`, `Properties`, `Contracts`, `Leads`) será responsável por:

- Suas próprias **migrations**, localizadas em `modules/Nome/Database/Migrations`
- Seus próprios **seeders**, localizados em `modules/Nome/Database/Seeders`
- Um **DatabaseSeeder** no módulo, que orquestra os seeders locais
- Suas validações, regras de negócio, eventos e exceções, de forma encapsulada

Além disso, o `DatabaseSeeder.php` principal da aplicação irá apenas **chamar os seeders principais de cada módulo**, mantendo o core da aplicação limpo e agnóstico aos domínios.

## Consequências

- A aplicação torna-se **altamente modular e independente por domínio**, facilitando a evolução, manutenção e testes unitários de cada componente.
- Torna-se possível reaproveitar módulos em outros projetos com pouca ou nenhuma modificação.
- O controle de dados sensíveis ou específicos fica descentralizado, respeitando os limites de contexto de cada módulo.
- A estrutura facilita a futura extração de domínios para microserviços, já que todo o conhecimento de negócio está isolado.

Essa abordagem reforça a visão de que **cada módulo deve ser tratado como um subproduto completo e autocontido**, responsável por sua própria lógica, dados e comportamento.