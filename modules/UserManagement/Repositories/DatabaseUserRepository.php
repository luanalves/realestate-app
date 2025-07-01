<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Repositories;

use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Contracts\UserRepositoryInterface;
use Modules\UserManagement\Models\User;

class DatabaseUserRepository implements UserRepositoryInterface
{
    /**
     * Find user by email with role relationship.
     * Direct database query without cache.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByEmailWithRole(string $email): User
    {
        return User::where('email', $email)
                  ->with('role')
                  ->firstOrFail();
    }

    /**
     * Find user by ID with role relationship.
     * Direct database query without cache.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdWithRole(int $userId): User
    {
        return User::where('id', $userId)
                  ->with('role')
                  ->firstOrFail();
    }

    /**
     * Invalidate cache for specific user.
     * No-op for database repository as there's no cache.
     */
    public function invalidateCache(int $userId): void
    {
        Log::debug('DatabaseUserRepository: Cache invalidation called but no cache present', [
            'user_id' => $userId,
        ]);

        // No cache to invalidate
    }

    /**
     * Clear all user caches.
     * No-op for database repository as there's no cache.
     */
    public function clearAllCache(): void
    {
        Log::debug('DatabaseUserRepository: Clear all cache called but no cache present');

        // No cache to clear
    }
}
