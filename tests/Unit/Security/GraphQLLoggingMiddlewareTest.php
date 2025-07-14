<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\Security;

use Modules\UserManagement\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Modules\Security\Http\Middleware\GraphQLLoggingMiddleware;
use Modules\UserManagement\Services\UserService;
use Tests\TestCase;

class GraphQLLoggingMiddlewareTest extends TestCase
{
    /**
     * Mock UserService for testing
     */
    protected $mockUserService;

    /**
     * Mock User for testing
     */
    protected $mockUser;

    /**
     * GraphQL Logging Middleware instance
     */
    protected $middleware;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock services
        $this->mockUserService = Mockery::mock(UserService::class);
        $this->mockUser = Mockery::mock(User::class)->makePartial();
        
        // Create middleware instance
        $this->middleware = new GraphQLLoggingMiddleware($this->mockUserService);
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
     * Test that introspection queries are not logged.
     */
    public function testIntrospectionQueryIsNotLogged(): void
    {
        $introspectionQuery = '
            query IntrospectionQuery {
                __schema {
                    queryType {
                        name
                    }
                }
            }
        ';
        
        // Create a mock request with introspection query
        $request = Request::create('/graphql', 'POST', [], [], [], [], json_encode([
            'query' => $introspectionQuery
        ]));
        $request->headers->set('Content-Type', 'application/json');
        
        // Test shouldLogOperation method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('shouldLogOperation');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->middleware, $request);
        
        $this->assertFalse($result, 'Introspection queries should not be logged');
    }

    /**
     * Test that __typename queries are not logged.
     */
    public function testTypenameQueryIsNotLogged(): void
    {
        $typenameQuery = '
            query {
                users {
                    __typename
                    id
                    name
                }
            }
        ';
        
        // Create a mock request with __typename query
        $request = Request::create('/graphql', 'POST', [], [], [], [], json_encode([
            'query' => $typenameQuery
        ]));
        $request->headers->set('Content-Type', 'application/json');
        
        // Test shouldLogOperation method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('shouldLogOperation');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->middleware, $request);
        
        $this->assertTrue($result, 'Regular queries with __typename should be logged');
    }

    /**
     * Test that regular user queries are logged.
     */
    public function testRegularQueryIsLogged(): void
    {
        $regularQuery = '
            query {
                users {
                    id
                    name
                    email
                }
            }
        ';
        
        // Create a mock request with regular query
        $request = Request::create('/graphql', 'POST', [], [], [], [], json_encode([
            'query' => $regularQuery
        ]));
        $request->headers->set('Content-Type', 'application/json');
        
        // Test shouldLogOperation method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('shouldLogOperation');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->middleware, $request);
        
        $this->assertTrue($result, 'Regular queries should be logged');
    }

    /**
     * Test that mutations are logged.
     */
    public function testMutationIsLogged(): void
    {
        $mutationQuery = '
            mutation {
                createUser(input: {
                    name: "Test User"
                    email: "test@example.com"
                }) {
                    id
                    name
                    email
                }
            }
        ';
        
        // Create a mock request with mutation query
        $request = Request::create('/graphql', 'POST', [], [], [], [], json_encode([
            'query' => $mutationQuery
        ]));
        $request->headers->set('Content-Type', 'application/json');
        
        // Test shouldLogOperation method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('shouldLogOperation');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->middleware, $request);
        
        $this->assertTrue($result, 'Mutations should be logged');
    }

    /**
     * Test extracting operation name from query.
     */
    public function testExtractOperationName(): void
    {
        $graphqlData = [
            'query' => '
                query {
                    users {
                        id
                        name
                    }
                }
            '
        ];
        
        // Test extractGraphQLOperation method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('extractGraphQLOperation');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->middleware, $graphqlData);
        
        $this->assertEquals('users', $result, 'Should extract operation name correctly');
    }

    /**
     * Test extracting operation name from mutation.
     */
    public function testExtractOperationNameFromMutation(): void
    {
        $graphqlData = [
            'query' => '
                mutation {
                    createUser(input: {
                        name: "Test"
                        email: "test@example.com"
                    }) {
                        id
                    }
                }
            '
        ];
        
        // Test extractGraphQLOperation method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('extractGraphQLOperation');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->middleware, $graphqlData);
        
        $this->assertEquals('createUser', $result, 'Should extract mutation name correctly');
    }

    /**
     * Test extracting module from operation name.
     */
    public function testExtractModuleFromOperation(): void
    {
        // Test extractModule method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('extractModule');
        $method->setAccessible(true);
        
        // Test user-related operations
        $this->assertEquals('UserManagement', $method->invoke($this->middleware, 'users'));
        $this->assertEquals('UserManagement', $method->invoke($this->middleware, 'createUser'));
        $this->assertEquals('UserManagement', $method->invoke($this->middleware, 'updateUser'));
        
        // Test security-related operations
        $this->assertEquals('Security', $method->invoke($this->middleware, 'securityLogs'));
        $this->assertEquals('Security', $method->invoke($this->middleware, 'securityLog'));
        
        // Test unknown operations
        $this->assertEquals('Unknown', $method->invoke($this->middleware, 'unknownOperation'));
    }

    /**
     * Test that excluded operations list contains expected operations.
     */
    public function testExcludedOperationsContainsExpectedOperations(): void
    {
        // Test that common introspection operations are excluded
        $reflectionClass = new \ReflectionClass($this->middleware);
        $excludedOperations = $reflectionClass->getConstant('EXCLUDED_OPERATIONS');
        
        $this->assertContains('IntrospectionQuery', $excludedOperations);
        $this->assertContains('__schema', $excludedOperations);
        $this->assertContains('__type', $excludedOperations);
        $this->assertContains('__typename', $excludedOperations);
    }

    /**
     * Test handling of malformed GraphQL queries.
     */
    public function testHandleMalformedQuery(): void
    {
        $malformedQuery = 'this is not a valid graphql query';
        
        // Create a mock request with malformed query
        $request = Request::create('/graphql', 'POST', [], [], [], [], json_encode([
            'query' => $malformedQuery
        ]));
        $request->headers->set('Content-Type', 'application/json');
        
        // Test shouldLogOperation method with malformed query
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('shouldLogOperation');
        $method->setAccessible(true);
        
        // Should not crash and should default to logging
        $result = $method->invoke($this->middleware, $request);
        
        $this->assertTrue($result, 'Malformed queries should default to being logged');
    }

    /**
     * Test extracting operation name from complex query.
     */
    public function testExtractOperationNameFromComplexQuery(): void
    {
        $graphqlData = [
            'query' => '
                query GetUsersWithRoles($first: Int!, $filter: UserFilter) {
                    users(first: $first, filter: $filter) {
                        data {
                            id
                            name
                            email
                            roles {
                                id
                                name
                            }
                        }
                        paginatorInfo {
                            total
                            hasMorePages
                        }
                    }
                }
            '
        ];
        
        // Test extractGraphQLOperation method using reflection
        $reflectionClass = new \ReflectionClass($this->middleware);
        $method = $reflectionClass->getMethod('extractGraphQLOperation');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->middleware, $graphqlData);
        
        $this->assertEquals('GetUsersWithRoles', $result, 'Should extract named operation from complex query');
    }
}
