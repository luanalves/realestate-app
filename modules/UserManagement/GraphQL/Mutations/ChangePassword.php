<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangePassword
{
    /**
     * Change the authenticated user's password.
     *
     * @param  null  $root
     * @param  array{current_password: string, new_password: string, new_password_confirmation: string}  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return array{success: bool, message: string}
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Ensure user is authenticated
        $user = Auth::guard('api')->user();
        if (!$user) {
            throw new AuthenticationException('You must be logged in to change your password');
        }

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

        return [
            'success' => true,
            'message' => 'Password has been changed successfully',
        ];
    }
}
