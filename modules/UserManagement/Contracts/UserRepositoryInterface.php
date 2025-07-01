<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Contracts;

use Modules\UserManagement\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find user by email with role relationship.
     *
     * @param string $email
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByEmailWithRole(string $email): User;

    /**
     * Find user by ID with role relationship.
     *
     * @param int $userId
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdWithRole(int $userId): User;

    /**
     * Invalidate cache for specific user.
     *
     * @param int $userId
     * @return void
     */
    public function invalidateCache(int $userId): void;

    /**
     * Clear all user caches.
     *
     * @return void
     */
    public function clearAllCache(): void;
}
