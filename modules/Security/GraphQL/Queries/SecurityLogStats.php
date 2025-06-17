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
use Modules\Security\Services\SecurityLogService;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SecurityLogStats
{
    private SecurityLogService $securityLogService;

    public function __construct(SecurityLogService $securityLogService)
    {
        $this->securityLogService = $securityLogService;
    }

    /**
     * Return security log statistics.
     *
     * @param mixed $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        // Check if user is authenticated
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('You need to be authenticated to access security log statistics');
        }

        $user = Auth::guard('api')->user();
        
        // Check if user has permission to view security logs
        // Only super_admin and real_estate_admin can view security logs
        if (!in_array($user->role?->name, ['super_admin', 'real_estate_admin'], true)) {
            throw new AuthenticationException('You do not have permission to access security log statistics');
        }

        $filters = $args['filter'] ?? [];

        return $this->securityLogService->getStatistics($filters);
    }
}
