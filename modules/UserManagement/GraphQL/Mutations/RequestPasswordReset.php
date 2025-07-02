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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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

    /**
     * Maximum number of password reset attempts allowed within the time window.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Time window for rate limiting in minutes.
     */
    private const DECAY_MINUTES = 60;

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

        $email = $args['email'];

        // Create a rate limiter key based on the email and IP address
        // This ensures we limit both per-user and per-IP to prevent different types of attacks
        $ipAddress = $context->request()->ip();
        $rateLimiterKey = Str::lower($email).'|'.$ipAddress;

        // Check if the rate limit has been exceeded
        if (RateLimiter::tooManyAttempts('password-reset:'.$rateLimiterKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn('password-reset:'.$rateLimiterKey);
            $minutes = ceil($seconds / 60);

            return [
                'success' => false,
                'message' => "Too many password reset attempts. Please try again after {$minutes} ".
                             ($minutes === 1 ? 'minute' : 'minutes').'.',
            ];
        }

        // Increment the rate limiter counter
        RateLimiter::hit('password-reset:'.$rateLimiterKey, self::DECAY_MINUTES * 60);

        // Send password reset link
        $status = Password::sendResetLink(['email' => $email]);

        // Return response based on the password broker's response
        return [
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ];
    }
}
