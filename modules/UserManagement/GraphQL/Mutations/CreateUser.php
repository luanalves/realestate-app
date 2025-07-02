<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Models\User;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateUser
{
    private UserManagementAuthorizationService $authService;

    public function __construct(UserManagementAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handles the GraphQL mutation to create a new user with the provided input data.
     *
     * Authorizes the operation, hashes the password, and creates a new user record with the specified attributes.
     *
     * @param mixed $rootValue The result from the parent resolver.
     * @param array $args The arguments passed to the mutation, including user input data.
     * @param GraphQLContext $context Shared context for the GraphQL request.
     * @param ResolveInfo $resolveInfo Information about the GraphQL query.
     * @return User The newly created user instance.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): User
    {
        $this->authService->authorizeUserManagementWrite();

        $input = $args['input'];

        // Hash the password
        $input['password'] = Hash::make($input['password']);

        // Create the user
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role_id' => $input['role_id'],
            'is_active' => $input['is_active'] ?? true, // Use input value or default to true
        ]);

        return $user;
    }
}
