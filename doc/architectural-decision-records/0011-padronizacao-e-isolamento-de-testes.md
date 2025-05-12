# ADR [0011]: Padronização e Isolamento de Testes Automatizados

**Status:** Aceito  
**Data:** 2025-04-17

## Contexto

Com a adoção de uma arquitetura modular baseada em Laravel e GraphQL (Lighthouse), tornou-se fundamental garantir a qualidade, previsibilidade e independência dos testes automatizados. O projeto utiliza múltiplos módulos, cada um com responsabilidades e domínios próprios, o que exige que os testes sejam organizados, isolados e padronizados para evitar efeitos colaterais e facilitar a manutenção.

Além disso, a integração com Docker e a necessidade de rodar testes em ambientes controlados reforçam a importância de práticas que garantam reprodutibilidade e independência dos testes em relação ao estado do banco de dados e de outros módulos.

## Decisão

- Todos os testes automatizados devem ser organizados nos diretórios `tests/Unit/` e `tests/Feature/`, com subdiretórios por módulo.
- Testes de GraphQL devem cobrir queries, mutations, autenticação, validação e casos de erro, conforme instruções do projeto.
- Testes devem ser independentes do estado do banco de dados, utilizando mocks sempre que possível.
- A autenticação nos testes deve ser feita via `Passport::actingAs()` com usuários mockados.
- O padrão de codificação e estrutura dos testes deve seguir o exemplo fornecido nas instruções do projeto, incluindo comentários, organização e uso de assertions significativas.
- Todos os comandos de teste devem ser executados dentro do container Docker, garantindo ambiente controlado e reprodutível.

## Consequências

- A base de testes se tornará mais confiável, previsível e fácil de manter.
- Novos desenvolvedores poderão entender rapidamente o padrão de testes e contribuir com qualidade.
- Redução de falhas intermitentes causadas por dependências de estado ou dados compartilhados.
- Facilita a evolução modular do sistema, permitindo que cada módulo seja testado e validado de forma isolada.
- Aumenta a confiança em deploys automatizados e integrações contínuas.
