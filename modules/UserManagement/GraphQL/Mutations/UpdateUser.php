<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Mutations;

use Modules\UserManagement\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Modules\UserManagement\Services\UserService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateUser
{
    private UserManagementAuthorizationService $authService;
    private UserService $userService;

    public function __construct(
        UserManagementAuthorizationService $authService,
        UserService $userService,
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * Update an existing user.
     *
     * @param mixed          $rootValue   The result from the parent resolver
     * @param array          $args        The arguments that were passed into the field
     * @param GraphQLContext $context     Arbitrary data that is shared between all fields of a single query
     * @param ResolveInfo    $resolveInfo Information about the query itself
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): User
    {
        // Get the target user ID
        $userId = (int) $args['id'];

        // Authorize: only self or admin can update
        $this->authService->authorizeUserModification($userId);

        $user = User::findOrFail($userId);
        $input = $args['input'];

        // Update user attributes if they are provided
        if (isset($input['name'])) {
            $user->name = $input['name'];
        }

        if (isset($input['email'])) {
            $user->email = $input['email'];
        }

        if (isset($input['password'])) {
            $user->password = Hash::make($input['password']);
        }

        // Role changes should only be allowed for admins
        if (isset($input['role_id'])) {
            // Only users with management permission can change roles
            $this->authService->authorizeUserManagementWrite();
            $user->role_id = $input['role_id'];
        }

        $user->save();

        // Invalidate user cache after update
        try {
            $this->userService->invalidateUserCache($userId);
            Log::info('User cache invalidated after update', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::warning('Failed to invalidate user cache', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }

        return $user;
    }
}
