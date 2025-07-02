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
     * Create a new user.
     *
     * @param mixed          $rootValue   The result from the parent resolver
     * @param array          $args        The arguments that were passed into the field
     * @param GraphQLContext $context     Arbitrary data that is shared between all fields of a single query
     * @param ResolveInfo    $resolveInfo Information about the query itself
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
