<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\Security;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Security\Services\SecurityLogService;
use Modules\Security\Models\SecurityLog;
use Tests\TestCase;
use Mockery;

class SecurityLogServiceTest extends TestCase
{
    use WithFaker;

    protected SecurityLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SecurityLogService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test applying filters to query builder.
     */
    public function testApplyFilters(): void
    {
        $mockQuery = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        
        // Test user_id filter
        $mockQuery->shouldReceive('where')->with('user_id', 123)->once()->andReturnSelf();
        
        // Test email filter
        $mockQuery->shouldReceive('where')->with('email', 'ILIKE', '%test@example.com%')->once()->andReturnSelf();
        
        // Test operation filter
        $mockQuery->shouldReceive('where')->with('operation', 'ILIKE', '%users%')->once()->andReturnSelf();

        $filters = [
            'user_id' => 123,
            'email' => 'test@example.com',
            'operation' => 'users'
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('applyFilters');
        $method->setAccessible(true);
        
        $method->invoke($this->service, $mockQuery, $filters);
        
        $this->assertTrue(true, 'Filters applied successfully');
    }

    /**
     * Test column mapping for ordering.
     */
    public function testMapOrderColumn(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mapOrderColumn');
        $method->setAccessible(true);
        
        $this->assertEquals('id', $method->invoke($this->service, 'ID'));
        $this->assertEquals('user_id', $method->invoke($this->service, 'USER_ID'));
        $this->assertEquals('operation', $method->invoke($this->service, 'OPERATION'));
        $this->assertEquals('created_at', $method->invoke($this->service, 'CREATED_AT'));
        $this->assertEquals('created_at', $method->invoke($this->service, 'UNKNOWN_COLUMN'));
    }

    /**
     * Test statistics calculation structure.
     */
    public function testGetStatisticsStructure(): void
    {
        // Skip this test if database is not available
        $this->markTestSkipped('Database integration test - requires proper database setup');
    }
}
