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
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class CreateUser
{
    /**
     * Create a new user.
     *
     * @param mixed $rootValue The result from the parent resolver
     * @param array $args The arguments that were passed into the field
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query
     * @param ResolveInfo $resolveInfo Information about the query itself
     * @return User
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): User
    {
        // Check if user is authenticated using the 'api' guard specifically
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to delete a user');
        }
        $input = $args['input'];
        
        // Hash the password
        $input['password'] = Hash::make($input['password']);
        
        // Create the user
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role_id' => $input['role_id'],
        ]);
        
        return $user;
    }
}