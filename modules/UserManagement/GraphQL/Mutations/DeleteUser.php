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
use Illuminate\Support\Facades\Log;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteUser
{
    private UserManagementAuthorizationService $authService;

    public function __construct(UserManagementAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Delete an existing user.
     *
     * @param mixed $rootValue The result from the parent resolver
     * @param array $args The arguments that were passed into the field
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query
     * @param ResolveInfo $resolveInfo Information about the query itself
     * @return array
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $this->authService->authorizeUserManagementWrite();
        
        $user = User::findOrFail($args['id']);
        
        try {
            $name = $user->name;
            $user->delete();
            
            return [
                'success' => true,
                'message' => "User {$name} deleted successfully"
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ];
        }
    }
}