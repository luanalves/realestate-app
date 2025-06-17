<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\Security\Services\SecurityAuthorizationService;
use Modules\Security\Services\SecurityLogService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SecurityLogStats
{
    private SecurityLogService $securityLogService;
    private SecurityAuthorizationService $authService;

    public function __construct(
        SecurityLogService $securityLogService,
        SecurityAuthorizationService $authService
    ) {
        $this->securityLogService = $securityLogService;
        $this->authService = $authService;
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
        $this->authService->authorizeSecurityLogAccess();

        $filters = $args['filter'] ?? [];

        return $this->securityLogService->getStatistics($filters);
    }
}
