<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Models\Role;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Roles
{
    /**
     * The cache key for storing all roles.
     */
    private const CACHE_KEY = 'user_management_roles';

    /**
     * The cache TTL in seconds (1 day).
     */
    private const CACHE_TTL = 86400;

    private UserManagementAuthorizationService $authService;

    /**
     * Initializes the Roles query with the provided authorization service.
     *
     * @param UserManagementAuthorizationService $authService Service used to enforce user management authorization.
     */
    public function __construct(UserManagementAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Retrieves all user roles, using cache for performance when available.
     *
     * Enforces authorization before fetching roles. Attempts to return roles from cache; if caching fails, logs a warning and fetches roles directly from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of all user roles.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Require authentication for security
        $this->authService->authorizeUserManagementRead();

        // Try to get roles from cache
        try {
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return Role::all();
            });
        } catch (\Exception $e) {
            // Log error but continue with uncached query
            Log::warning('Failed to get roles from cache', [
                'error' => $e->getMessage(),
            ]);

            // Fallback to direct database query
            return Role::all();
        }
    }

    /**
     * Clears the cached user roles to ensure updated role data is served.
     *
     * Should be called after creating, updating, or deleting roles to maintain cache consistency.
     */
    public static function invalidateCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
            Log::info('Roles cache invalidated');
        } catch (\Exception $e) {
            Log::error('Failed to invalidate roles cache', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
