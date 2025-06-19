<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RealEstates
{
    /**
     * @var RealEstateService
     */
    private RealEstateService $_realEstateService;

    /**
     * Constructor.
     *
     * @param RealEstateService $realEstateService
     */
    public function __construct(RealEstateService $realEstateService)
    {
        $this->_realEstateService = $realEstateService;
    }

    /**
     * Return a paginated list of real estate agencies.
     *
     * @param mixed $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return LengthAwarePaginator
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): LengthAwarePaginator
    {
        // Authorize access to real estate data
        $user = $this->_realEstateService->authorizeRealEstateAccess();

        $query = RealEstate::query();

        // Apply multi-tenant filtering for non-admin users
        if ($user->role && $user->role->name !== RolesSeeder::ROLE_SUPER_ADMIN) {
            // Filter by tenant for regular users
            if ($user->tenant_id) {
                $query->where('tenant_id', $user->tenant_id);
            }
        }

        $first = $args['first'] ?? 10;
        $page = $args['page'] ?? 1;

        // Apply ordering if specified
        if (isset($args['orderBy']) && is_array($args['orderBy'])) {
            foreach ($args['orderBy'] as $orderBy) {
                $direction = $orderBy['order'] === 'DESC' ? 'desc' : 'asc';
                $query->orderBy($orderBy['column'], $direction);
            }
        } else {
            // Default ordering
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($first, ['*'], 'page', $page);
    }
}
