<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */
declare(strict_types=1);

namespace Modules\Security\Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Security\Http\Middleware\GraphQLAuditLogger;
use Modules\Security\Services\AuditLoggerService;
use PHPUnit\Framework\TestCase;

class GraphQLAuditLoggerTest extends TestCase
{
    public function testItCallsAuditLoggerServiceOnGraphqlRequest(): void
    {
        $middleware = new GraphQLAuditLogger();
        $request = Request::create('/graphql', 'POST', [
            'query' => 'mutation { __typename }',
            'variables' => ['foo' => 'bar'],
        ]);
        $user = (object) ['id' => 1, 'email' => 'test@example.com'];
        Auth::shouldReceive('user')->andReturn($user);
        $response = new Response('{"data":{"__typename":"Mutation"}}', 200);

        $called = false;
        AuditLoggerService::shouldReceive('logRequest')->once()->andReturnUsing(function ($meta, $operation, $status, $details) use (&$called) {
            $called = true;
            $this->assertEquals('Mutation', $operation);
            $this->assertEquals(200, $status);
            $this->assertArrayHasKey('uuid', $meta);
            $this->assertEquals('test@example.com', $meta['email']);
        });

        $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertTrue($called, 'AuditLoggerService::logRequest should be called.');
    }
}
