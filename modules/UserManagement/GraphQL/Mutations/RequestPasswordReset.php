<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RequestPasswordReset
{
    /**
     * Request a password reset link to be sent to the user's email.
     *
     * @param null                 $root
     * @param array{email: string} $args
     *
     * @return array{success: bool, message: string}
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Validate email
        $validator = Validator::make($args, [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        // Send password reset link
        $status = Password::sendResetLink(['email' => $args['email']]);

        // Return response based on the password broker's response
        return [
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ];
    }
}
