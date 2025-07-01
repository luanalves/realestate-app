<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class UserManagementAuthorizationService
{
    /**
     * Roles that have access to user management operations.
     */
    private const AUTHORIZED_ROLES = [
        RolesSeeder::ROLE_SUPER_ADMIN,
        RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
    ];

    /**
     * Check if user is authenticated and authorized for read-only user management operations.
     * This is used for queries that only read user data.
     *
     * @throws AuthenticationException
     */
    public function authorizeUserManagementRead(): User
    {
        return $this->authorizeUserManagementAccess();
    }

    /**
     * Check if user is authenticated and authorized to access user management.
     *
     * @throws AuthenticationException
     */
    public function authorizeUserManagementAccess(): User
    {
        // Check if user is authenticated
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to access user management');
        }

        $user = Auth::guard('api')->user();

        // Check if user has permission to manage users
        if (!$this->hasUserManagementPermission($user)) {
            throw new AuthenticationException('You do not have permission to access user management');
        }

        return $user;
    }

    /**
     * Check if user is authenticated (for read-only operations).
     *
     * @throws AuthenticationException
     */
    public function requireAuthentication(): User
    {
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to access this resource');
        }

        return Auth::guard('api')->user();
    }

    /**
     * Check if user has permission to manage users.
     */
    public function hasUserManagementPermission(?User $user): bool
    {
        if (!$user || !$user->role) {
            return false;
        }

        return in_array($user->role->name, self::AUTHORIZED_ROLES, true);
    }

    /**
     * Get list of authorized roles for user management.
     */
    public function getAuthorizedRoles(): array
    {
        return self::AUTHORIZED_ROLES;
    }

    /**
     * Check if a specific role has access to user management.
     */
    public function isRoleAuthorized(string $roleName): bool
    {
        return in_array($roleName, self::AUTHORIZED_ROLES, true);
    }

    /**
     * Check if current user can perform destructive operations (create, update, delete).
     *
     * @throws AuthenticationException
     */
    public function authorizeUserManagementWrite(): User
    {
        $user = $this->authorizeUserManagementAccess();

        return $user;
    }

    /**
     * Authorize user modification (update/delete) based on role and ownership.
     * Only allow if:
     * 1. Current user is the same as target user (self modification)
     * 2. Current user has admin privileges
     *
     * @param int $targetUserId The ID of the user being modified
     * @return User The authenticated user making the request
     * @throws AuthenticationException If user is not authorized
     */
    public function authorizeUserModification(int $targetUserId): User
    {
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to modify user data');
        }

        $currentUser = Auth::guard('api')->user();
        
        // Self-modification is always allowed
        if ($currentUser->id === $targetUserId) {
            return $currentUser;
        }
        
        // Otherwise, check if user has management permissions
        if (!$this->hasUserManagementPermission($currentUser)) {
            throw new AuthenticationException('You do not have permission to modify other users');
        }
        
        return $currentUser;
    }
}
