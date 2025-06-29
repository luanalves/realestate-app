<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Queries;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Services\UserService;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Me
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Return the currently authenticated user with cache support.
     *
     * @param null $root
     *
     * @throws AuthenticationException
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
