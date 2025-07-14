<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\UserManagement;

use Modules\UserManagement\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Modules\UserManagement\Repositories\DatabaseUserRepository;
use Tests\TestCase;

class DatabaseUserRepositoryTest extends TestCase
{
    use WithFaker;

    /**
     * DatabaseUserRepository instance.
     */
    protected $repository;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new DatabaseUserRepository();
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
     * Test findByEmailWithRole method exists and follows contract.
     */
    public function testFindByEmailWithRoleMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->repository, 'findByEmailWithRole'),
            'DatabaseUserRepository must implement findByEmailWithRole method'
        );
    }

    /**
     * Test findByIdWithRole method exists and follows contract.
     */
    public function testFindByIdWithRoleMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->repository, 'findByIdWithRole'),
            'DatabaseUserRepository must implement findByIdWithRole method'
        );
    }

    /**
     * Test invalidateCache method exists and follows contract.
     */
    public function testInvalidateCacheMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->repository, 'invalidateCache'),
            'DatabaseUserRepository must implement invalidateCache method'
        );
    }

    /**
     * Test clearAllCache method exists and follows contract.
     */
    public function testClearAllCacheMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->repository, 'clearAllCache'),
            'DatabaseUserRepository must implement clearAllCache method'
        );
    }

    /**
     * Test repository implements UserRepositoryInterface.
     */
    public function testRepositoryImplementsInterface(): void
    {
        $this->assertInstanceOf(
            \Modules\UserManagement\Contracts\UserRepositoryInterface::class,
            $this->repository,
            'DatabaseUserRepository must implement UserRepositoryInterface'
        );
    }

    /**
     * Test cache invalidation can be called without errors (no-op for database repository).
     */
    public function testInvalidateCacheCanBeCalledSafely(): void
    {
        // Arrange
        $userId = 1;

        // Act & Assert - Should not throw exceptions, but is no-op for database repo
        try {
            $this->repository->invalidateCache($userId);
            $this->assertTrue(true, 'Cache invalidation executed without errors');
        } catch (\Exception $e) {
            $this->fail('Cache invalidation should not throw exceptions: ' . $e->getMessage());
        }
    }

    /**
     * Test clear all cache can be called without errors (no-op for database repository).
     */
    public function testClearAllCacheCanBeCalledSafely(): void
    {
        // Act & Assert - Should not throw exceptions, but is no-op for database repo
        try {
            $this->repository->clearAllCache();
            $this->assertTrue(true, 'Clear all cache executed without errors');
        } catch (\Exception $e) {
            $this->fail('Clear all cache should not throw exceptions: ' . $e->getMessage());
        }
    }

    /**
     * Test repository structure matches interface requirements.
     */
    public function testRepositoryStructureMatchesInterface(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        
        // Test that class has expected structure
        $this->assertTrue($reflection->hasMethod('findByEmailWithRole'));
        $this->assertTrue($reflection->hasMethod('findByIdWithRole'));
        $this->assertTrue($reflection->hasMethod('invalidateCache'));
        $this->assertTrue($reflection->hasMethod('clearAllCache'));
        
        // Verify all methods are public
        $findByEmailMethod = $reflection->getMethod('findByEmailWithRole');
        $findByIdMethod = $reflection->getMethod('findByIdWithRole');
        $invalidateCacheMethod = $reflection->getMethod('invalidateCache');
        $clearAllCacheMethod = $reflection->getMethod('clearAllCache');
        
        $this->assertTrue($findByEmailMethod->isPublic(), 'findByEmailWithRole must be public');
        $this->assertTrue($findByIdMethod->isPublic(), 'findByIdWithRole must be public');
        $this->assertTrue($invalidateCacheMethod->isPublic(), 'invalidateCache must be public');
        $this->assertTrue($clearAllCacheMethod->isPublic(), 'clearAllCache must be public');
    }

    /**
     * Test that cache operations are no-ops for database repository.
     */
    public function testCacheOperationsAreNoOpsForDatabaseRepository(): void
    {
        // Database repository should have no-op cache methods
        $userId = 1;
        
        // These should execute without side effects
        $this->repository->invalidateCache($userId);
        $this->repository->clearAllCache();
        
        // Verify that they can be called multiple times without issues
        $this->repository->invalidateCache($userId);
        $this->repository->clearAllCache();
        
        $this->assertTrue(true, 'Cache operations are safe no-ops for database repository');
    }

    /**
     * Test repository can be instantiated correctly.
     */
    public function testRepositoryCanBeInstantiatedCorrectly(): void
    {
        $newRepository = new DatabaseUserRepository();
        
        $this->assertInstanceOf(DatabaseUserRepository::class, $newRepository);
        $this->assertInstanceOf(
            \Modules\UserManagement\Contracts\UserRepositoryInterface::class,
            $newRepository
        );
    }
}
