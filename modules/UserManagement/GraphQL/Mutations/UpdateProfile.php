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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Modules\UserManagement\Services\UserService;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateProfile
{
    private UserService $userService;
    private UserManagementAuthorizationService $authService;

    public function __construct(
        UserService $userService,
        UserManagementAuthorizationService $authService
    ) {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param null                                         $root
     * @param array{name: string|null, email: string|null} $args
     *
     * @return array{success: bool, message: string, user: User|null}
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Ensure user is authenticated - uses service for consistent authorization
        $user = $this->authService->requireAuthentication();

        // Validate inputs
        $validator = Validator::make($args, [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'unique:users,email,'.$user->id,
            ],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
                'user' => null,
            ];
        }

        // Only update fields that are actually provided
        $updates = [];
        if (isset($args['name'])) {
            $updates['name'] = $args['name'];
        }
        if (isset($args['email'])) {
            $updates['email'] = $args['email'];
        }

        // Update the user profile
        if (!empty($updates)) {
            /* @var User $user */
            $user->update($updates);
            
            // Invalidate user cache after update
            try {
                $this->userService->invalidateUserCache($user->id);
                Log::info("User cache invalidated after profile update", ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::warning("Failed to invalidate user cache after profile update", [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user,
        ];
    }
}
