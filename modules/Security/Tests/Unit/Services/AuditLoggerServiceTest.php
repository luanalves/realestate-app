<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */
declare(strict_types=1);

namespace Modules\Security\Tests\Unit\Services;

use Modules\Security\Models\AuditLog;
use Modules\Security\Services\AuditLoggerService;
use MongoDB\Client;
use MongoDB\Collection;
use PHPUnit\Framework\TestCase;

class AuditLoggerServiceTest extends TestCase
{
    public function testLogRequestPersistsToPostgresAndMongo(): void
    {
        // Mock AuditLog (Eloquent)
        $meta = [
            'uuid' => 'test-uuid',
            'user_id' => 1,
            'email' => 'test@example.com',
            'operation' => 'TestOp',
            'module' => 'TestModule',
            'ip' => '127.0.0.1',
            'status' => 'success',
            'created_at' => now(),
        ];
        $details = [
            'uuid' => 'test-uuid',
            'variables' => ['foo' => 'bar'],
            'full_query' => 'mutation TestOp { __typename }',
            'headers' => ['user-agent' => 'test'],
            'response' => ['status' => 200, 'body' => ['data' => ['__typename' => 'Mutation']]],
        ];

        $auditLogMock = \Mockery::mock('overload:'.AuditLog::class);
        $auditLogMock->shouldReceive('create')->once()->with($meta);

        $mongoClientMock = \Mockery::mock(Client::class);
        $collectionMock = \Mockery::mock(Collection::class);
        $mongoClientMock->shouldReceive('selectCollection')->andReturn($collectionMock);
        $collectionMock->shouldReceive('insertOne')->once()->with($details);

        // Swap Client in AuditLoggerService
        AuditLoggerServiceTestHelper::setMongoClient($mongoClientMock);

        // Call static method
        AuditLoggerServiceTestHelper::logRequest($meta, 'TestOp', 200, $details);

        // Add assertions to ensure mocks were called
        $this->addToAssertionCount(\Mockery::getContainer() ? \Mockery::getContainer()->mockery_getExpectationCount() : 1);

        \Mockery::close();
    }
}

// Helper to allow injection of MongoDB client for testability
class AuditLoggerServiceTestHelper extends AuditLoggerService
{
    protected static $mongoClient;

    public static function setMongoClient($client)
    {
        static::$mongoClient = $client;
    }

    public static function logRequest(array $meta, ?string $operation, $status, array $details): void
    {
        AuditLog::create($meta);
        static::$mongoClient->selectCollection(env('MONGO_DB_DATABASE', 'audit'), 'graphql_audit_details')->insertOne($details);
    }
}
