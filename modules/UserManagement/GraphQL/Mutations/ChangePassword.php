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
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Modules\UserManagement\Services\UserService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangePassword
{
    private UserService $userService;
    private UserManagementAuthorizationService $authService;

    /**
     * Initializes the ChangePassword mutation with user and authorization services.
     *
     * @param UserService $userService Service for user-related operations.
     * @param UserManagementAuthorizationService $authService Service for enforcing authentication.
     */
    public function __construct(
        UserService $userService,
        UserManagementAuthorizationService $authService
    ) {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    /**
     * Handles the password change mutation for an authenticated user.
     *
     * Validates the provided current password, ensures the new password meets security requirements, updates the user's password, and invalidates the user cache. Returns a success status and message indicating the result.
     *
     * @param null $root
     * @param array{current_password: string, new_password: string, new_password_confirmation: string} $args The password change input arguments.
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array{success: bool, message: string} Result of the password change operation.
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Ensure user is authenticated - uses service for consistent authorization
        $user = $this->authService->requireAuthentication();

        // Validate inputs
        $validator = Validator::make($args, [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Your current password is incorrect.');
                }
            }],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'different:current_password',
            ],
            'new_password_confirmation' => [
                'required',
                'same:new_password',
            ],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        // Update the password
        /** @var User $user */
        $user->password = Hash::make($args['new_password']);
        $user->save();
        
        // Invalidate user cache after password change
        try {
            $this->userService->invalidateUserCache($user->id);
            Log::info("User cache invalidated after password change", ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::warning("Failed to invalidate user cache after password change", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return [
            'success' => true,
            'message' => 'Password has been changed successfully',
        ];
    }
}
