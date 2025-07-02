<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Queries;

use Modules\UserManagement\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Services\UserService;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Me
{
    private UserService $userService;

    /**
     * Initializes the Me query resolver with the provided user service.
     *
     * @param UserService $userService Service used to retrieve user data.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Resolves the currently authenticated user for the GraphQL "me" query, including role information.
     *
     * Attempts to return cached user data with role details via the UserService. If the cache retrieval fails, falls back to returning the authenticated user with the role relationship loaded.
     *
     * @throws AuthenticationException If no user is authenticated.
     * @return User The authenticated user with role information.
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): User
    {
        // Get the authenticated user from Laravel Passport
        $user = Auth::guard('api')->user();

        if (!$user) {
            throw new AuthenticationException('You must be logged in to view your profile');
        }

        // Use UserService to get cached user data with role relationship
        try {
            return $this->userService->getUserById($user->id);
        } catch (\Exception $e) {
            // Log error but fallback to direct user with role relationship
            Log::warning('Me query: Failed to get cached user data, falling back to direct query', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return $user->load('role');
        }
    }
}
