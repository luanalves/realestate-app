<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\UserManagement;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\UserManagement\Factories\UserRepositoryFactory;
use Modules\UserManagement\Repositories\CachedUserRepository;
use Modules\UserManagement\Repositories\DatabaseUserRepository;
use Tests\TestCase;

class UserRepositoryFactoryTest extends TestCase
{
    use WithFaker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
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
     * Test factory create method returns appropriate repository.
     */
    public function testFactoryCreateReturnsRepository(): void
    {
        // Act
        $repository = UserRepositoryFactory::create();

        // Assert - Should return either cached or database repository based on cache availability
        $this->assertTrue(
            $repository instanceof CachedUserRepository || $repository instanceof DatabaseUserRepository,
            'Factory must return either CachedUserRepository or DatabaseUserRepository'
        );

        $this->assertInstanceOf(
            \Modules\UserManagement\Contracts\UserRepositoryInterface::class,
            $repository,
            'Factory result must implement UserRepositoryInterface'
        );
    }

    /**
     * Test factory create with force cache parameter.
     */
    public function testFactoryCreateWithForceCacheParameter(): void
    {
        // Test forcing cached repository
        $cachedRepo = UserRepositoryFactory::create(true);
        $this->assertInstanceOf(CachedUserRepository::class, $cachedRepo);

        // Test forcing database repository
        $dbRepo = UserRepositoryFactory::create(false);
        $this->assertInstanceOf(DatabaseUserRepository::class, $dbRepo);
    }

    /**
     * Test factory createCached method.
     */
    public function testFactoryCreateCachedMethod(): void
    {
        // Act
        $repository = UserRepositoryFactory::createCached();

        // Assert
        $this->assertInstanceOf(CachedUserRepository::class, $repository);
        $this->assertInstanceOf(
            \Modules\UserManagement\Contracts\UserRepositoryInterface::class,
            $repository
        );
    }

    /**
     * Test factory createDatabase method.
     */
    public function testFactoryCreateDatabaseMethod(): void
    {
        // Act
        $repository = UserRepositoryFactory::createDatabase();

        // Assert
        $this->assertInstanceOf(DatabaseUserRepository::class, $repository);
        $this->assertInstanceOf(
            \Modules\UserManagement\Contracts\UserRepositoryInterface::class,
            $repository
        );
    }

    /**
     * Test factory getCacheInfo method.
     */
    public function testFactoryGetCacheInfoMethod(): void
    {
        // Act
        $cacheInfo = UserRepositoryFactory::getCacheInfo();

        // Assert
        $this->assertIsArray($cacheInfo);
        $this->assertArrayHasKey('default_cache_store', $cacheInfo);
        $this->assertArrayHasKey('is_available', $cacheInfo);
        $this->assertArrayHasKey('redis_connection', $cacheInfo);
        $this->assertArrayHasKey('cache_prefix', $cacheInfo);
        $this->assertIsBool($cacheInfo['is_available']);
    }

    /**
     * Test factory methods exist and are accessible.
     */
    public function testFactoryMethodsExistAndAreAccessible(): void
    {
        $reflection = new \ReflectionClass(UserRepositoryFactory::class);

        // Test public methods exist
        $this->assertTrue($reflection->hasMethod('create'));
        $this->assertTrue($reflection->hasMethod('createCached'));
        $this->assertTrue($reflection->hasMethod('createDatabase'));
        $this->assertTrue($reflection->hasMethod('getCacheInfo'));

        // Test methods are static and public
        $createMethod = $reflection->getMethod('create');
        $this->assertTrue($createMethod->isStatic());
        $this->assertTrue($createMethod->isPublic());

        $cachedMethod = $reflection->getMethod('createCached');
        $this->assertTrue($cachedMethod->isStatic());
        $this->assertTrue($cachedMethod->isPublic());

        $databaseMethod = $reflection->getMethod('createDatabase');
        $this->assertTrue($databaseMethod->isStatic());
        $this->assertTrue($databaseMethod->isPublic());

        $infoMethod = $reflection->getMethod('getCacheInfo');
        $this->assertTrue($infoMethod->isStatic());
        $this->assertTrue($infoMethod->isPublic());
    }

    /**
     * Test factory can handle different cache scenarios gracefully.
     */
    public function testFactoryHandlesCacheScenariosGracefully(): void
    {
        // Test that factory doesn't throw exceptions regardless of cache state
        try {
            $repo1 = UserRepositoryFactory::create();
            $repo2 = UserRepositoryFactory::create(true);
            $repo3 = UserRepositoryFactory::create(false);
            $repo4 = UserRepositoryFactory::createCached();
            $repo5 = UserRepositoryFactory::createDatabase();

            $this->assertTrue(true, 'Factory handles all scenarios without exceptions');
        } catch (\Exception $e) {
            $this->fail('Factory should handle all scenarios gracefully: '.$e->getMessage());
        }
    }

    /**
     * Test factory returns consistent results for same parameters.
     */
    public function testFactoryReturnsConsistentResults(): void
    {
        // Test that same parameters return same type of repository
        $repo1 = UserRepositoryFactory::create(true);
        $repo2 = UserRepositoryFactory::create(true);

        $this->assertEquals(get_class($repo1), get_class($repo2));

        $repo3 = UserRepositoryFactory::create(false);
        $repo4 = UserRepositoryFactory::create(false);

        $this->assertEquals(get_class($repo3), get_class($repo4));
    }

    /**
     * Test factory cache info structure is stable.
     */
    public function testFactoryCacheInfoStructureIsStable(): void
    {
        // Get cache info multiple times
        $info1 = UserRepositoryFactory::getCacheInfo();
        $info2 = UserRepositoryFactory::getCacheInfo();

        // Should have same keys
        $this->assertEquals(array_keys($info1), array_keys($info2));

        // Should have consistent structure
        foreach (['default_cache_store', 'is_available', 'redis_connection', 'cache_prefix'] as $key) {
            $this->assertArrayHasKey($key, $info1, "Cache info should always have key: {$key}");
        }
    }

    /**
     * Test factory integration with dependency injection.
     */
    public function testFactoryIntegrationWithDependencyInjection(): void
    {
        // Test that factory can be used in app container
        $appRepository = app()->make(\Modules\UserManagement\Contracts\UserRepositoryInterface::class);

        $this->assertInstanceOf(
            \Modules\UserManagement\Contracts\UserRepositoryInterface::class,
            $appRepository
        );

        $this->assertTrue(
            $appRepository instanceof CachedUserRepository || $appRepository instanceof DatabaseUserRepository
        );
    }
}
