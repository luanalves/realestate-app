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
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Modules\UserManagement\Services\UserService;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteUser
{
    private UserManagementAuthorizationService $authService;
    private UserService $userService;

    /**
     * Initializes the DeleteUser mutation with authorization and user services.
     *
     * @param UserManagementAuthorizationService $authService Service for user management authorization checks.
     * @param UserService $userService Service for user-related operations such as cache invalidation.
     */
    public function __construct(
        UserManagementAuthorizationService $authService,
        UserService $userService,
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * Handles the deletion of a user via a GraphQL mutation, enforcing authorization and preventing self-deletion.
     *
     * Prevents users from deleting their own accounts and ensures only authorized users can perform deletions. After successful deletion, attempts to invalidate the deleted user's cache. Returns an array indicating success or failure, along with a relevant message.
     *
     * @param mixed $rootValue The result from the parent resolver.
     * @param array $args Arguments passed to the mutation, including the user ID to delete.
     * @param GraphQLContext $context Shared context for the GraphQL request.
     * @param ResolveInfo $resolveInfo Information about the GraphQL query.
     * @return array Result of the deletion operation, including success status and message.
     * @throws AuthenticationException If a user attempts to delete their own account.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Get the target user ID
        $userId = (int) $args['id'];

        // Self-deletion is not allowed - only admins can delete users
        // We don't use authorizeUserModification here because we have a stricter policy for deletion
        $currentUser = $this->authService->authorizeUserManagementWrite();

        // Prevent users from deleting themselves to avoid orphaned sessions
        if ($currentUser->id === $userId) {
            throw new AuthenticationException('You cannot delete your own account. Please contact another administrator.');
        }

        $user = User::findOrFail($userId);

        try {
            $name = $user->name;
            $userId = $user->id; // Store ID before deletion for cache invalidation

            // Delete the user
            $user->delete();

            // Invalidate user cache after deletion
            try {
                $this->userService->invalidateUserCache($userId);
                Log::info('User cache invalidated after deletion', ['user_id' => $userId]);
            } catch (\Exception $cacheException) {
                Log::warning('Failed to invalidate user cache after deletion', [
                    'user_id' => $userId,
                    'error' => $cacheException->getMessage(),
                ]);
            }

            return [
                'success' => true,
                'message' => "User {$name} deleted successfully",
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting user: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error deleting user: '.$e->getMessage(),
            ];
        }
    }
}
