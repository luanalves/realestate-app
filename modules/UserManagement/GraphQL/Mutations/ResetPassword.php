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
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ResetPassword
{
    /**
     * Reset the user's password using a token.
     *
     * @param  null  $root
     * @param  array{email: string, token: string, password: string, password_confirmation: string}  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return array{success: bool, message: string}
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Validate input
        $validator = Validator::make($args, [
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        // Attempt to reset the user's password
        $status = Password::reset(
            [
                'email' => $args['email'],
                'token' => $args['token'],
                'password' => $args['password'],
                'password_confirmation' => $args['password_confirmation'],
            ],
            function (User $user, string $password) {
                // Update the user's password
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Fire the PasswordReset event
                event(new PasswordReset($user));
            }
        );

        // Return response based on the password broker's response
        return [
            'success' => $status === Password::PASSWORD_RESET,
            'message' => __($status),
        ];
    }
}
