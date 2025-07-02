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
     * Retrieves a user by email, including the associated role.
     *
     * Performs a direct database query and returns the user with the related role data.
     *
     * @param string $email The email address of the user to retrieve.
     * @return User The user model with the role relationship loaded.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no user is found with the given email.
     */
    public function findByEmailWithRole(string $email): User
    {
        return User::where('email', $email)
                  ->with('role')
                  ->firstOrFail();
    }

    /**
     * Retrieves a user by ID, including the associated role, using a direct database query.
     *
     * @param int $userId The unique identifier of the user.
     * @return User The user model with the related role loaded.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no user with the given ID is found.
     */
    public function findByIdWithRole(int $userId): User
    {
        return User::where('id', $userId)
                  ->with('role')
                  ->firstOrFail();
    }

    /**
     * No-op method for invalidating cache for a specific user.
     *
     * This method does nothing because the database repository does not use caching.
     *
     * @param int $userId The ID of the user for which cache invalidation was requested.
     */
    public function invalidateCache(int $userId): void
    {
        Log::debug('DatabaseUserRepository: Cache invalidation called but no cache present', [
            'user_id' => $userId,
        ]);

        // No cache to invalidate
    }

    /**
     * Placeholder method for clearing all user caches; performs no action as caching is not implemented.
     */
    public function clearAllCache(): void
    {
        Log::debug('DatabaseUserRepository: Clear all cache called but no cache present');

        // No cache to clear
    }
}
