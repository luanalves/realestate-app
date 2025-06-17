<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\Security\Models\SecurityLog as SecurityLogModel;
use Modules\Security\Services\SecurityAuthorizationService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SecurityLogQuery
{
    private SecurityAuthorizationService $authService;

    public function __construct(SecurityAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Return a specific security log by ID.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?SecurityLogModel
    {
        $this->authService->authorizeSecurityLogAccess();

        $id = $args['id'];

        return SecurityLogModel::with('user')
            ->find($id);
    }
}
