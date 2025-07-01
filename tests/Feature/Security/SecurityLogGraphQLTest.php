<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Feature\Security;

use Modules\UserManagement\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;

class SecurityLogGraphQLTest extends TestCase
{
    use WithFaker;
    
    /**
     * Mock user for testing
     */
    protected $mockUser;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock user for authentication
        $this->mockUser = Mockery::mock(User::class)->makePartial();
        $this->mockUser->shouldReceive('getAuthIdentifier')->andReturn(1);
        $this->mockUser->shouldReceive('withAccessToken')->andReturnSelf();
        
        // Authenticate with Laravel Passport
        Passport::actingAs($this->mockUser);
    }
    
    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test getting a list of security logs through GraphQL.
     */
    public function testQuerySecurityLogs(): void
    {
        // Make the GraphQL request
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    securityLogs(first: 10) {
                        data {
                            id
                            user_id
                            email
                            operation
                            module
                            ip_address
                            user_agent
                            created_at
                        }
                        paginatorInfo {
                            count
                            currentPage
                            firstItem
                            hasMorePages
                            lastItem
                            lastPage
                            perPage
                            total
                        }
                    }
                }
            '
        ]);
        
        // Assert response meets expectations
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'securityLogs' => [
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'email',
                            'operation',
                            'module',
                            'ip_address',
                            'user_agent',
                            'created_at'
                        ]
                    ],
                    'paginatorInfo' => [
                        'count',
                        'currentPage',
                        'firstItem',
                        'hasMorePages',
                        'lastItem',
                        'lastPage',
                        'perPage',
                        'total'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test getting a single security log through GraphQL.
     */
    public function testQuerySingleSecurityLog(): void
    {
        // First, let's get a list to find an ID
        $logsResponse = $this->postJson('/graphql', [
            'query' => '
                query {
                    securityLogs(first: 1) {
                        data {
                            id
                        }
                    }
                }
            '
        ]);
        
        $logsResponse->assertStatus(200);
        $logs = $logsResponse->json('data.securityLogs.data');
        
        if (empty($logs)) {
            $this->markTestSkipped('No security logs available for testing');
        }
        
        $logId = $logs[0]['id'];
        
        // Now test getting the single log
        $response = $this->postJson('/graphql', [
            'query' => '
                query($id: ID!) {
                    securityLog(id: $id) {
                        id
                        user_id
                        email
                        operation
                        module
                        ip_address
                        user_agent
                        created_at
                        details {
                            request_data
                            response_data
                            execution_time
                        }
                    }
                }
            ',
            'variables' => [
                'id' => $logId
            ]
        ]);
        
        // Assert response meets expectations
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'securityLog' => [
                    'id',
                    'user_id',
                    'email',
                    'operation',
                    'module',
                    'ip_address',
                    'user_agent',
                    'created_at',
                    'details' => [
                        'request_data',
                        'response_data',
                        'execution_time'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test getting security log statistics through GraphQL.
     */
    public function testQuerySecurityLogStats(): void
    {
        // Make the GraphQL request
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    securityLogStats {
                        total_logs
                        logs_today
                        logs_this_week
                        logs_this_month
                        top_operations {
                            operation
                            count
                        }
                        top_modules {
                            module
                            count
                        }
                        top_users {
                            email
                            count
                        }
                    }
                }
            '
        ]);
        
        // Assert response meets expectations
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'securityLogStats' => [
                    'total_logs',
                    'logs_today',
                    'logs_this_week',
                    'logs_this_month',
                    'top_operations' => [
                        '*' => [
                            'operation',
                            'count'
                        ]
                    ],
                    'top_modules' => [
                        '*' => [
                            'module',
                            'count'
                        ]
                    ],
                    'top_users' => [
                        '*' => [
                            'email',
                            'count'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test security logs with filters.
     */
    public function testQuerySecurityLogsWithFilters(): void
    {
        // Make the GraphQL request with filters
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    securityLogs(
                        first: 10,
                        filter: {
                            operation: "users",
                            module: "UserManagement"
                        }
                    ) {
                        data {
                            id
                            operation
                            module
                        }
                        paginatorInfo {
                            total
                        }
                    }
                }
            '
        ]);
        
        // Assert response meets expectations
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'securityLogs' => [
                    'data' => [
                        '*' => [
                            'id',
                            'operation',
                            'module'
                        ]
                    ],
                    'paginatorInfo' => [
                        'total'
                    ]
                ]
            ]
        ]);
        
        // Verify filters are applied (if there are results)
        $logs = $response->json('data.securityLogs.data');
        foreach ($logs as $log) {
            if ($log['operation'] !== null) {
                $this->assertEquals('users', $log['operation']);
            }
            if ($log['module'] !== null) {
                $this->assertEquals('UserManagement', $log['module']);
            }
        }
    }

    /**
     * Test authentication is required for security log queries.
     */
    public function testAuthenticationRequiredForSecurityLogs(): void
    {
        // Remove authentication
        Passport::actingAs(null);
        
        // Make the GraphQL request without authentication
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    securityLogs(first: 10) {
                        data {
                            id
                            operation
                        }
                    }
                }
            '
        ]);
        
        // Assert that authentication error is returned
        $response->assertJson([
            'errors' => [
                [
                    'message' => 'Unauthenticated.'
                ]
            ]
        ]);
    }

    /**
     * Test date range filters work correctly.
     */
    public function testQuerySecurityLogsWithDateRange(): void
    {
        $startDate = now()->subDays(7)->toISOString();
        $endDate = now()->toISOString();
        
        // Make the GraphQL request with date filters
        $response = $this->postJson('/graphql', [
            'query' => '
                query($startDate: DateTime, $endDate: DateTime) {
                    securityLogs(
                        first: 10,
                        filter: {
                            created_at_start: $startDate,
                            created_at_end: $endDate
                        }
                    ) {
                        data {
                            id
                            created_at
                        }
                        paginatorInfo {
                            total
                        }
                    }
                }
            ',
            'variables' => [
                'startDate' => $startDate,
                'endDate' => $endDate
            ]
        ]);
        
        // Assert response meets expectations
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'securityLogs' => [
                    'data' => [
                        '*' => [
                            'id',
                            'created_at'
                        ]
                    ],
                    'paginatorInfo' => [
                        'total'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test ordering of security logs.
     */
    public function testQuerySecurityLogsWithOrdering(): void
    {
        // Make the GraphQL request with ordering
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    securityLogs(
                        first: 5,
                        orderBy: [
                            {
                                column: CREATED_AT,
                                order: DESC
                            }
                        ]
                    ) {
                        data {
                            id
                            created_at
                        }
                    }
                }
            '
        ]);
        
        // Assert response meets expectations
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'securityLogs' => [
                    'data' => [
                        '*' => [
                            'id',
                            'created_at'
                        ]
                    ]
                ]
            ]
        ]);
        
        // Verify ordering (if there are multiple results)
        $logs = $response->json('data.securityLogs.data');
        if (count($logs) > 1) {
            $previousDate = null;
            foreach ($logs as $log) {
                if ($previousDate !== null) {
                    $this->assertGreaterThanOrEqual(
                        strtotime($log['created_at']),
                        strtotime($previousDate),
                        'Logs should be ordered by created_at DESC'
                    );
                }
                $previousDate = $log['created_at'];
            }
        }
    }
}
