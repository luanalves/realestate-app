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

class SecurityLogs
{
    private SecurityLogService $securityLogService;

    public function __construct(SecurityLogService $securityLogService)
    {
        $this->securityLogService = $securityLogService;
    }

    /**
     * Return a paginated list of security logs with filters.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
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

        $filters = $args['filter'] ?? [];
        $orderBy = $args['orderBy'] ?? [['column' => 'CREATED_AT', 'order' => 'DESC']];
        $first = $args['first'] ?? 20;
        $page = $args['page'] ?? 1;

        // Get paginated results
        $paginatedLogs = $this->securityLogService->getFilteredLogs(
            $filters,
            $orderBy,
            $first,
            $page
        );

        return [
            'data' => $paginatedLogs->items(),
            'paginatorInfo' => [
                'count' => $paginatedLogs->count(),
                'currentPage' => $paginatedLogs->currentPage(),
                'firstItem' => $paginatedLogs->firstItem(),
                'hasMorePages' => $paginatedLogs->hasMorePages(),
                'lastItem' => $paginatedLogs->lastItem(),
                'lastPage' => $paginatedLogs->lastPage(),
                'perPage' => $paginatedLogs->perPage(),
                'total' => $paginatedLogs->total(),
            ],
        ];
    }
}
