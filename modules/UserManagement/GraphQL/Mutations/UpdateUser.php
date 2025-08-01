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
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateUser
{
    private UserManagementAuthorizationService $authService;

    public function __construct(UserManagementAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Update an existing user.
     *
     * @param mixed $rootValue The result from the parent resolver
     * @param array $args The arguments that were passed into the field
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query
     * @param ResolveInfo $resolveInfo Information about the query itself
     * @return User
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): User
    {
        $this->authService->authorizeUserManagementWrite();
        
        $user = User::findOrFail($args['id']);
        $input = $args['input'];
        
        // Update user attributes if they are provided
        if (isset($input['name'])) {
            $user->name = $input['name'];
        }
        
        if (isset($input['email'])) {
            $user->email = $input['email'];
        }
        
        if (isset($input['password'])) {
            $user->password = Hash::make($input['password']);
        }
        
        if (isset($input['role_id'])) {
            $user->role_id = $input['role_id'];
        }
        
        $user->save();
        
        return $user;
    }
}