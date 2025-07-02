<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Factories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Contracts\UserRepositoryInterface;
use Modules\UserManagement\Repositories\CachedUserRepository;
use Modules\UserManagement\Repositories\DatabaseUserRepository;

class UserRepositoryFactory
{
    /**
     * Creates and returns a user repository instance, selecting between cached or database-backed implementations.
     *
     * If $forceCache is provided, it forces the use of either the cached or database repository. If not provided, the method automatically selects the cached repository if cache is available, otherwise defaults to the database repository.
     *
     * @param bool|null $forceCache If true, always use the cached repository; if false, always use the database repository; if null, auto-detect based on cache availability.
     * @return UserRepositoryInterface The selected user repository implementation.
     */
    public static function create(?bool $forceCache = null): UserRepositoryInterface
    {
        // Allow manual override for testing or specific use cases
        if ($forceCache !== null) {
            return $forceCache
                ? new CachedUserRepository()
                : new DatabaseUserRepository();
        }

        // Auto-detect best strategy
        if (self::isCacheAvailable()) {
            return new CachedUserRepository();
        }

        return new DatabaseUserRepository();
    }

    /**
     * Creates and returns a new instance of CachedUserRepository.
     *
     * @return CachedUserRepository The cached user repository instance.
     */
    public static function createCached(): CachedUserRepository
    {
        return new CachedUserRepository();
    }

    /**
     * Creates and returns a new instance of the database-backed user repository.
     *
     * @return DatabaseUserRepository The user repository implementation that interacts directly with the database.
     */
    public static function createDatabase(): DatabaseUserRepository
    {
        return new DatabaseUserRepository();
    }

    /**
     * Determines whether the cache system is available and functioning correctly.
     *
     * Performs a write-read-delete test on the cache and returns true if the cache responds as expected; otherwise, returns false.
     *
     * @return bool True if the cache is available and working; false otherwise.
     */
    private static function isCacheAvailable(): bool
    {
        try {
            // Test cache connectivity
            $testKey = 'user_repository_factory_test_'.time();
            $testValue = 'test';

            Cache::put($testKey, $testValue, 5);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);

            $isAvailable = $retrieved === $testValue;

            if (!$isAvailable) {
                Log::warning('UserRepositoryFactory: Cache test failed - value mismatch', [
                    'expected' => $testValue,
                    'retrieved' => $retrieved,
                ]);
            }

            return $isAvailable;
        } catch (\Exception $e) {
            Log::warning('UserRepositoryFactory: Cache is not available', [
                'error' => $e->getMessage(),
                'cache_driver' => config('cache.default'),
            ]);

            return false;
        }
    }

    /**
     * Retrieves current cache configuration and status details for debugging purposes.
     *
     * @return array An associative array containing the default cache store, cache availability status, Redis connection configuration, and cache prefix.
     */
    public static function getCacheInfo(): array
    {
        return [
            'default_cache_store' => config('cache.default'),
            'is_available' => self::isCacheAvailable(),
            'redis_connection' => config('cache.stores.redis.connection', 'not_configured'),
            'cache_prefix' => config('cache.prefix'),
        ];
    }
}
