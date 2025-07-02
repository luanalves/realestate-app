<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Repositories;

use Modules\UserManagement\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Contracts\UserRepositoryInterface;

class CachedUserRepository implements UserRepositoryInterface
{
    private const USER_EMAIL_CACHE_TTL = 900; // 15 minutes
    private const USER_ID_CACHE_TTL = 900; // 15 minutes
    private const CACHE_PREFIX = 'user_management';

    /**
     * Retrieves a user by email, including the related role, using cache for performance.
     *
     * Attempts to fetch the user from cache; if not found, queries the database and caches the result.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no user with the given email exists.
     * @return User The user model with the associated role.
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

    /****
     * Retrieves a user by ID along with their role, using cache for faster access.
     *
     * Attempts to fetch the user and their associated role from the cache. If not found, queries the database and caches the result for future requests.
     *
     * @param int $userId The ID of the user to retrieve.
     * @return User The user model with the associated role.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no user with the given ID exists.
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
     * Removes cached entries for a specific user by user ID, clearing both email and ID-based cache keys if the user exists.
     *
     * @param int $userId The ID of the user whose cache should be invalidated.
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
     * Clears all cached user data using cache tags if supported.
     *
     * If cache tags are not available or an error occurs, logs a warning with details and a suggestion for manual or pattern-based cache clearing.
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
     * Generates a cache key for user email lookups by combining a prefix, the string "email", and an MD5 hash of the lowercase email address.
     *
     * @param string $email The user's email address.
     * @return string The generated cache key.
     */
    private function getUserEmailCacheKey(string $email): string
    {
        return self::CACHE_PREFIX.':email:'.md5(strtolower($email));
    }

    /**
     * Generates a cache key for user ID lookups by combining the cache prefix with the user ID.
     *
     * @param int $userId The user ID for which to generate the cache key.
     * @return string The generated cache key.
     */
    private function getUserIdCacheKey(int $userId): string
    {
        return self::CACHE_PREFIX.':id:'.$userId;
    }
}
