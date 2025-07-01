<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Queries;

use Modules\UserManagement\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Users
{
    private UserManagementAuthorizationService $authService;

    public function __construct(UserManagementAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Return a list of all users.
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->authService->authorizeUserManagementRead();
        
        return User::with('role')->get();
    }
}