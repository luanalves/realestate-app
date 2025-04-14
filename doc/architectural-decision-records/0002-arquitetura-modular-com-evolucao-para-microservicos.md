# ADR [0002]: Arquitetura Modular Com Evolucao Para Microservicos

**Status:** Aceito  
**Data:** 2025-03-24

## Contexto

Durante a definição da arquitetura do projeto imobiliário, surgiu a necessidade de organizar o sistema de forma que ele fosse ao mesmo tempo eficiente no curto prazo e escalável no longo prazo. A equipe possui experiência consolidada com PHP, o que favorece entregas ágeis e com alta qualidade neste stack.

No entanto, também foi identificado que determinadas funcionalidades no futuro — especialmente as mais sensíveis à performance, integração externa ou independência de domínio — poderão se beneficiar de abordagens específicas em outras linguagens ou serviços isolados. Por isso, optamos por uma estratégia que não engesse a tecnologia desde o início.

## Decisão

A arquitetura da aplicação será baseada em módulos organizados por domínio funcional (ex: UserManagement, Properties, Leads, Contratos). Cada módulo conterá seu próprio contexto isolado, incluindo migrations, seeders, rotas, schemas GraphQL, e lógica de negócio.

Essa organização modular permitirá que:

- A equipe desenvolva com agilidade usando as ferramentas e linguagem que mais domina (PHP);
- Cada módulo evolua de forma isolada, respeitando seu próprio ciclo de vida;
- Módulos com gargalos específicos ou oportunidades de especialização possam ser extraídos como **microserviços** no futuro, em qualquer linguagem mais apropriada para o contexto;
- A escalabilidade aconteça de forma **progressiva e não forçada**.


## Consequências

A aplicação passa a seguir um padrão claro de separação de responsabilidades, aumentando a manutenibilidade e facilitando o onboarding de novos desenvolvedores. 

O uso de Laravel continua a oferecer produtividade e padronização no início, ao passo que a modularização prepara o terreno para uma futura migração parcial ou total para arquitetura de microserviços.

Cada nova funcionalidade deverá ser avaliada para determinar se se encaixa melhor em um módulo existente ou se merece um novo domínio isolado. Além disso, decisões como divisão de bancos, cache e filas por módulo tornam-se naturais dentro dessa abordagem.
