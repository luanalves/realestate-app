<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Modules\Security\Models\SecurityLog as SecurityLogModel;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SecurityLogQuery
{
    /**
     * Return a specific security log by ID.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?SecurityLogModel
    {
        // Check if user is authenticated
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to access security logs');
        }

        $user = Auth::guard('api')->user();

        // Check if user has permission to view security logs
        // Only super_admin and real_estate_admin can view security logs
        if (!in_array($user->role?->name, ['super_admin', 'real_estate_admin'], true)) {
            throw new AuthenticationException('You do not have permission to access security logs');
        }

        $id = $args['id'];

        return SecurityLogModel::with('user')
            ->find($id);
    }
}
