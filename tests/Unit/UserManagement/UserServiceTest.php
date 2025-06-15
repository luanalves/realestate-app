<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\UserManagement;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\UserManagement\Contracts\UserRepositoryInterface;
use Modules\UserManagement\Services\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use WithFaker;

    /**
     * Mock repository for testing.
     */
    protected $mockRepository;

    /**
     * UserService instance.
     */
    protected $userService;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = \Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->mockRepository);
    }

    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * Test getAuthenticatedUserData method exists and follows contract.
     */
    public function testGetAuthenticatedUserDataMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->userService, 'getAuthenticatedUserData'),
            'UserService must implement getAuthenticatedUserData method'
        );
    }

    /**
     * Test getUserById method exists and follows contract.
     */
    public function testGetUserByIdMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->userService, 'getUserById'),
            'UserService must implement getUserById method'
        );
    }

    /**
     * Test invalidateUserCache method exists and follows contract.
     */
    public function testInvalidateUserCacheMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->userService, 'invalidateUserCache'),
            'UserService must implement invalidateUserCache method'
        );
    }

    /**
     * Test clearAllUserCache method exists and follows contract.
     */
    public function testClearAllUserCacheMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->userService, 'clearAllUserCache'),
            'UserService must implement clearAllUserCache method'
        );
    }

    /**
     * Test getDebugInfo method exists and follows contract.
     */
    public function testGetDebugInfoMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->userService, 'getDebugInfo'),
            'UserService must implement getDebugInfo method'
        );
    }

    /**
     * Test formatUserForResponse method exists and follows contract.
     */
    public function testFormatUserForResponseMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->userService, 'formatUserForResponse'),
            'UserService must implement formatUserForResponse method'
        );
    }

    /**
     * Test getAuthenticatedUserData delegates to repository findByEmailWithRole.
     */
    public function testGetAuthenticatedUserDataDelegatesToRepository(): void
    {
        // Arrange
        $email = $this->faker->email;
        $mockUser = \Mockery::mock(User::class)->makePartial();
        $mockUser->id = 1;
        $mockUser->email = $email;
        $mockUser->role = null;

        $this->mockRepository
            ->shouldReceive('findByEmailWithRole')
            ->with($email)
            ->once()
            ->andReturn($mockUser);

        // Act
        $result = $this->userService->getAuthenticatedUserData($email);

        // Assert
        $this->assertEquals($mockUser, $result);
    }

    /**
     * Test getUserById delegates to repository findByIdWithRole.
     */
    public function testGetUserByIdDelegatesToRepository(): void
    {
        // Arrange
        $userId = 1;
        $mockUser = \Mockery::mock(User::class)->makePartial();
        $mockUser->id = $userId;

        $this->mockRepository
            ->shouldReceive('findByIdWithRole')
            ->with($userId)
            ->once()
            ->andReturn($mockUser);

        // Act
        $result = $this->userService->getUserById($userId);

        // Assert
        $this->assertEquals($mockUser, $result);
    }

    /**
     * Test invalidateUserCache delegates to repository.
     */
    public function testInvalidateUserCacheDelegatesToRepository(): void
    {
        // Arrange
        $userId = 1;

        $this->mockRepository
            ->shouldReceive('invalidateCache')
            ->with($userId)
            ->once();

        // Act & Assert - Should not throw exceptions
        $this->userService->invalidateUserCache($userId);
        $this->assertTrue(true, 'Cache invalidation completed successfully');
    }

    /**
     * Test clearAllUserCache delegates to repository.
     */
    public function testClearAllUserCacheDelegatesToRepository(): void
    {
        $this->mockRepository
            ->shouldReceive('clearAllCache')
            ->once();

        // Act & Assert - Should not throw exceptions
        $this->userService->clearAllUserCache();
        $this->assertTrue(true, 'Clear all cache completed successfully');
    }

    /**
     * Test getDebugInfo returns expected structure.
     */
    public function testGetDebugInfoReturnsExpectedStructure(): void
    {
        // Act
        $result = $this->userService->getDebugInfo();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('repository_class', $result);
        $this->assertArrayHasKey('cache_info', $result);
        $this->assertArrayHasKey('timestamp', $result);
    }

    /**
     * Test formatUserForResponse returns expected structure.
     */
    public function testFormatUserForResponseReturnsExpectedStructure(): void
    {
        // Arrange
        $mockUser = \Mockery::mock(User::class);
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mockUser->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $mockUser->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $mockUser->shouldReceive('getAttribute')->with('role')->andReturn(null);
        $mockUser->shouldReceive('getAttribute')->with('created_at')->andReturn(null);
        $mockUser->shouldReceive('getAttribute')->with('updated_at')->andReturn(null);

        // Act
        $result = $this->userService->formatUserForResponse($mockUser);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('role', $result);
    }

    /**
     * Test service can be instantiated with and without repository injection.
     */
    public function testServiceCanBeInstantiatedCorrectly(): void
    {
        // Test with repository injection
        $serviceWithInjection = new UserService($this->mockRepository);
        $this->assertInstanceOf(UserService::class, $serviceWithInjection);

        // Test without repository injection (uses factory)
        $serviceWithFactory = new UserService();
        $this->assertInstanceOf(UserService::class, $serviceWithFactory);
    }

    /**
     * Test service methods handle repository exceptions gracefully.
     */
    public function testServiceMethodsHandleRepositoryExceptions(): void
    {
        // Arrange
        $email = $this->faker->email;
        $this->mockRepository
            ->shouldReceive('findByEmailWithRole')
            ->with($email)
            ->once()
            ->andThrow(new \Exception('Repository error'));

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Repository error');

        $this->userService->getAuthenticatedUserData($email);
    }

    /**
     * Test all service methods maintain their contracts.
     */
    public function testServiceMethodsMaintainContracts(): void
    {
        $reflection = new \ReflectionClass($this->userService);

        // Test that all expected methods exist and are public
        $expectedMethods = [
            'getAuthenticatedUserData',
            'getUserById',
            'invalidateUserCache',
            'clearAllUserCache',
            'getDebugInfo',
            'formatUserForResponse',
        ];

        foreach ($expectedMethods as $methodName) {
            $this->assertTrue($reflection->hasMethod($methodName), "Method {$methodName} must exist");

            $method = $reflection->getMethod($methodName);
            $this->assertTrue($method->isPublic(), "Method {$methodName} must be public");
        }
    }
}
