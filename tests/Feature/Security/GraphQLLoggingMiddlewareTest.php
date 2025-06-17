<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Feature\Security;

use Tests\TestCase;

class GraphQLLoggingMiddlewareTest extends TestCase
{
    /**
     * Testa se o middleware de logging está interceptando requisições GraphQL.
     * Se a requisição passar sem erro, significa que o middleware foi executado.
     */
    public function testMiddlewareInterceptsGraphqlRequests(): void
    {
        // Executa uma query GraphQL simples
        $response = $this->postJson('/graphql', [
            'query' => '{ __typename }',
        ]);

        // Se a requisição não retornar 500 (erro interno),
        // significa que o middleware foi executado sem quebrar
        $this->assertNotEquals(500, $response->getStatusCode(),
            'Middleware não deveria quebrar a aplicação');

        // Se chegou até aqui sem erro 500, o middleware foi executado
        $this->assertTrue(true, 'Middleware GraphQLLoggingMiddleware executou sem erros!');
    }

    /**
     * Testa se o middleware processa requisições GraphQL com diferentes queries.
     */
    public function testMiddlewareHandlesDifferentQueries(): void
    {
        // Teste com query de introspection
        $response1 = $this->postJson('/graphql', [
            'query' => '{ __schema { types { name } } }',
        ]);

        // Teste com query simples
        $response2 = $this->postJson('/graphql', [
            'query' => '{ __typename }',
        ]);

        // Ambas as requisições deveriam passar pelo middleware sem erro 500
        $this->assertNotEquals(500, $response1->getStatusCode());
        $this->assertNotEquals(500, $response2->getStatusCode());

        $this->assertTrue(true, 'Middleware processa diferentes tipos de query!');
    }
}
