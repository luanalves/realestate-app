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
     * Create appropriate user repository based on cache availability and configuration.
     *
     * @param bool|null $forceCache Override automatic detection
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
     * Create cached repository explicitly.
     */
    public static function createCached(): CachedUserRepository
    {
        return new CachedUserRepository();
    }

    /**
     * Create database repository explicitly.
     */
    public static function createDatabase(): DatabaseUserRepository
    {
        return new DatabaseUserRepository();
    }

    /**
     * Check if cache is available and working.
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
     * Get current cache configuration for debugging.
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
