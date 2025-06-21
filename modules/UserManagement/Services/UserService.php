<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Contracts\UserRepositoryInterface;
use Modules\UserManagement\Factories\UserRepositoryFactory;

class UserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(?UserRepositoryInterface $userRepository = null)
    {
        // Allow dependency injection for testing, otherwise use factory
        $this->userRepository = $userRepository ?? UserRepositoryFactory::create();
    }

    /**
     * Get authenticated user data for login response.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getAuthenticatedUserData(string $email): User
    {
        try {
            $user = $this->userRepository->findByEmailWithRole($email);

            return $user;
        } catch (\Exception $e) {
            Log::error('UserService: Failed to get user data', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get user by ID with role information.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserById(int $userId): User
    {
        return $this->userRepository->findByIdWithRole($userId);
    }

    /**
     * Invalidate cache for specific user.
     * Useful when user data is updated.
     */
    public function invalidateUserCache(int $userId): void
    {
        $this->userRepository->invalidateCache($userId);
    }

    /**
     * Clear all user caches.
     * Administrative function for cache management.
     */
    public function clearAllUserCache(): void
    {
        $this->userRepository->clearAllCache();
    }

    /**
     * Get cache and repository information for debugging.
     */
    public function getDebugInfo(): array
    {
        return [
            'repository_class' => get_class($this->userRepository),
            'cache_info' => UserRepositoryFactory::getCacheInfo(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Format user data for API response.
     */
    public function formatUserForResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'description' => $user->role->description,
            ] : null,
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }
}
