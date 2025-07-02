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

    /**
     * Initializes the UpdateUser mutation with authorization and user service dependencies.
     *
     * @param UserManagementAuthorizationService $authService Service for performing authorization checks.
     * @param UserService $userService Service for user-related operations such as cache management.
     */
    public function __construct(
        UserManagementAuthorizationService $authService,
        UserService $userService,
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * Handles the GraphQL mutation to update an existing user's information.
     *
     * Updates user attributes such as name, email, password, and optionally role, with appropriate authorization checks. Only the user themselves or an admin can perform updates, and role changes require admin privileges. After saving changes, the user's cache is invalidated.
     *
     * @param mixed $rootValue The result from the parent resolver.
     * @param array $args The arguments passed to the mutation, including user ID and input fields.
     * @param GraphQLContext $context Shared context for the GraphQL request.
     * @param ResolveInfo $resolveInfo Information about the GraphQL query.
     * @return User The updated user model instance.
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
