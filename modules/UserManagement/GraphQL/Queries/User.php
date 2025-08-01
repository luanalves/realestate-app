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
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class User
{
    private UserManagementAuthorizationService $authService;

    public function __construct(UserManagementAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get a user by ID.
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return \App\Models\User
     * @throws \Exception
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->authService->authorizeUserManagementRead();

        // Find the user by ID
        $user = UserModel::with('role')->find($args['id']);

        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }
}