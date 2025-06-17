<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class SecurityAuthorizationService
{
    /**
     * Roles that have access to security logs.
     */
    private const AUTHORIZED_ROLES = [
        RolesSeeder::ROLE_SUPER_ADMIN,
        RolesSeeder::ROLE_REAL_ESTATE_ADMIN,
    ];

    /**
     * Check if user is authenticated and authorized to access security logs.
     *
     * @throws AuthenticationException
     */
    public function authorizeSecurityLogAccess(): User
    {
        // Check if user is authenticated
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to access security logs');
        }

        $user = Auth::guard('api')->user();

        // Check if user has permission to view security logs
        if (!$this->hasSecurityLogPermission($user)) {
            throw new AuthenticationException('You do not have permission to access security logs');
        }

        return $user;
    }

    /**
     * Check if user has permission to access security logs.
     */
    public function hasSecurityLogPermission(?User $user): bool
    {
        if (!$user || !$user->role) {
            return false;
        }

        return in_array($user->role->name, self::AUTHORIZED_ROLES, true);
    }

    /**
     * Get list of authorized roles for security logs.
     */
    public function getAuthorizedRoles(): array
    {
        return self::AUTHORIZED_ROLES;
    }

    /**
     * Check if a specific role has access to security logs.
     */
    public function isRoleAuthorized(string $roleName): bool
    {
        return in_array($roleName, self::AUTHORIZED_ROLES, true);
    }
}
