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
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdatePreferences
{
    /**
     * Handles the GraphQL mutation to update the authenticated user's preferences.
     *
     * Validates the input and updates the user's preferences if authenticated. Returns a response indicating success or failure, along with a message and the updated preferences if successful.
     *
     * @param null $root
     * @param array{preferences: array} $args The mutation arguments, including the preferences to update.
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array{success: bool, message: string, preferences: array|null} The result of the update operation.
     * @throws AuthenticationException If the user is not authenticated.
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Ensure user is authenticated
        $user = Auth::guard('api')->user();
        if (!$user) {
            throw new AuthenticationException('You must be logged in to update your preferences');
        }

        // Validate inputs
        $validator = Validator::make($args, [
            'preferences' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
                'preferences' => null,
            ];
        }

        // Update the user preferences
        /** @var User $user */
        $user->preferences = $args['preferences'];
        $user->save();

        return [
            'success' => true,
            'message' => 'Preferences updated successfully',
            'preferences' => $user->preferences,
        ];
    }
}
