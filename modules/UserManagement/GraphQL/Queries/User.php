<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Queries;

use App\Models\User as UserModel;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class User
{
    /**
     * Get a user by ID.
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return \App\Models\User
     * @throws \Nuwave\Lighthouse\Exceptions\AuthenticationException
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Check if user is authenticated using the 'api' guard specifically
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to access this resource');
        }

        // Find the user by ID
        $user = UserModel::with('role')->find($args['id']);

        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }
}