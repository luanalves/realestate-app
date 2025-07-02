<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Queries;

use Modules\UserManagement\Models\User as UserModel;
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
     * Resolves a GraphQL query to retrieve a user by their ID, including their role.
     *
     * Authorizes the request for user management read access before fetching the user.
     * Throws an exception if the user with the specified ID does not exist.
     *
     * @param mixed $rootValue The root value passed to the resolver.
     * @param array $args The arguments provided to the query, must include 'id'.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context The GraphQL context.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo GraphQL resolve information.
     * @return \Modules\UserManagement\Models\User The user model with related role data.
     * @throws \Exception If the user is not found.
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