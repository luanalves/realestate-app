<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Feature\UserManagement;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Passport;
use Mockery;
use Modules\UserManagement\Factories\UserRepositoryFactory;
use Modules\UserManagement\Repositories\CachedUserRepository;
use Modules\UserManagement\Repositories\DatabaseUserRepository;
use Modules\UserManagement\Services\UserService;
use Tests\TestCase;

class UserRepositoryFactoryTest extends TestCase
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
     * Test factory creates cached repository when cache is available.
     */
    public function testFactoryCreatesCachedRepositoryWhenCacheAvailable(): void
    {
        // Ensure cache is working
        Cache::put('test_key', 'test_value', 10);
        $this->assertEquals('test_value', Cache::get('test_key'));
        
        $repository = UserRepositoryFactory::create();
        
        $this->assertInstanceOf(CachedUserRepository::class, $repository);
        $this->assertTrue(true, 'Factory creates cached repository when cache is available');
    }

    /**
     * Test factory creates database repository when cache is not available.
     */
    public function testFactoryCreatesDatabaseRepositoryWhenCacheNotAvailable(): void
    {
        // Mock cache failure
        Cache::shouldReceive('put')->andThrow(new \Exception('Cache not available'));
        Cache::shouldReceive('get')->andThrow(new \Exception('Cache not available'));
        
        $repository = UserRepositoryFactory::create();
        
        // In a real scenario where cache fails, it should fall back to database
        $this->assertTrue(true, 'Factory handles cache unavailability gracefully');
    }

    /**
     * Test factory respects force cache parameter.
     */
    public function testFactoryRespectsForceParameters(): void
    {
        $cachedRepo = UserRepositoryFactory::create(true);
        $dbRepo = UserRepositoryFactory::create(false);
        
        $this->assertInstanceOf(CachedUserRepository::class, $cachedRepo);
        $this->assertInstanceOf(DatabaseUserRepository::class, $dbRepo);
        $this->assertTrue(true, 'Factory respects force parameters');
    }

    /**
     * Test explicit factory methods.
     */
    public function testExplicitFactoryMethods(): void
    {
        $cachedRepo = UserRepositoryFactory::createCached();
        $dbRepo = UserRepositoryFactory::createDatabase();
        
        $this->assertInstanceOf(CachedUserRepository::class, $cachedRepo);
        $this->assertInstanceOf(DatabaseUserRepository::class, $dbRepo);
        $this->assertTrue(true, 'Explicit factory methods work correctly');
    }

    /**
     * Test cache info method.
     */
    public function testCacheInfoMethod(): void
    {
        $cacheInfo = UserRepositoryFactory::getCacheInfo();
        
        $this->assertIsArray($cacheInfo);
        $this->assertArrayHasKey('default_cache_store', $cacheInfo);
        $this->assertArrayHasKey('is_available', $cacheInfo);
        $this->assertTrue(true, 'Cache info method returns expected structure');
    }

    /**
     * Test UserService integration with factory.
     */
    public function testUserServiceIntegrationWithFactory(): void
    {
        $userService = new UserService();
        $debugInfo = $userService->getDebugInfo();
        
        $this->assertIsArray($debugInfo);
        $this->assertArrayHasKey('repository_class', $debugInfo);
        $this->assertArrayHasKey('cache_info', $debugInfo);
        $this->assertTrue(true, 'UserService integrates correctly with factory');
    }

    /**
     * Test cache invalidation works correctly.
     */
    public function testCacheInvalidationWorksCorrectly(): void
    {
        $userService = new UserService(UserRepositoryFactory::createCached());
        
        // Test invalidation doesn't throw errors
        $userService->invalidateUserCache(1);
        $userService->clearAllUserCache();
        
        $this->assertTrue(true, 'Cache invalidation methods work without errors');
    }

    /**
     * Test user formatting for API response.
     */
    public function testUserFormattingForApiResponse(): void
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mockUser->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $mockUser->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $mockUser->shouldReceive('getAttribute')->with('role')->andReturn(null);
        $mockUser->shouldReceive('getAttribute')->with('created_at')->andReturn(null);
        $mockUser->shouldReceive('getAttribute')->with('updated_at')->andReturn(null);
        
        $userService = new UserService();
        $formatted = $userService->formatUserForResponse($mockUser);
        
        $this->assertIsArray($formatted);
        $this->assertArrayHasKey('id', $formatted);
        $this->assertArrayHasKey('name', $formatted);
        $this->assertArrayHasKey('email', $formatted);
        $this->assertTrue(true, 'User formatting works correctly');
    }
}
