<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Contracts\UserRepositoryInterface;

class CachedUserRepository implements UserRepositoryInterface
{
    private const USER_EMAIL_CACHE_TTL = 900; // 15 minutes
    private const USER_ID_CACHE_TTL = 900; // 15 minutes
    private const CACHE_PREFIX = 'user_management';

    /**
     * Find user by email with role relationship.
     * Searches cache first, falls back to database.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByEmailWithRole(string $email): User
    {
        $cacheKey = $this->getUserEmailCacheKey($email);

        return Cache::remember($cacheKey, self::USER_EMAIL_CACHE_TTL, function () use ($email) {
            return User::where('email', $email)
                      ->with('role')
                      ->firstOrFail();
        });
    }

    /**
     * Find user by ID with role relationship.
     * Searches cache first, falls back to database.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdWithRole(int $userId): User
    {
        $cacheKey = $this->getUserIdCacheKey($userId);

        return Cache::remember($cacheKey, self::USER_ID_CACHE_TTL, function () use ($userId) {
            return User::where('id', $userId)
                      ->with('role')
                      ->firstOrFail();
        });
    }

    /**
     * Invalidate cache for specific user.
     */
    public function invalidateCache(int $userId): void
    {
        $user = User::find($userId);

        if ($user) {
            $emailCacheKey = $this->getUserEmailCacheKey($user->email);
            $idCacheKey = $this->getUserIdCacheKey($userId);

            Cache::forget($emailCacheKey);
            Cache::forget($idCacheKey);
        }
    }

    /**
     * Clear all user caches.
     * Uses cache tags if available, otherwise logs warning.
     */
    public function clearAllCache(): void
    {
        try {
            // Try to use cache tags if Redis supports it
            Cache::tags([self::CACHE_PREFIX])->flush();
        } catch (\Exception $e) {
            Log::warning('UserRepository: Could not clear cache using tags', [
                'error' => $e->getMessage(),
                'suggestion' => 'Consider manual cache clearing or implementing pattern-based clearing',
            ]);
        }
    }

    /**
     * Generate cache key for user email lookup.
     */
    private function getUserEmailCacheKey(string $email): string
    {
        return self::CACHE_PREFIX.':email:'.md5(strtolower($email));
    }

    /**
     * Generate cache key for user ID lookup.
     */
    private function getUserIdCacheKey(int $userId): string
    {
        return self::CACHE_PREFIX.':id:'.$userId;
    }
}
