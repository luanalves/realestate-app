<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Collection;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AddressesByRealEstateId
{
    private RealEstateService $_realEstateService;

    /**
     * Constructor.
     */
    public function __construct(RealEstateService $realEstateService)
    {
        $this->_realEstateService = $realEstateService;
    }

    /**
     * Get all addresses for a specific real estate agency.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Collection
    {
        // Find the real estate
        $realEstate = RealEstate::findOrFail($args['realEstateId']);

        // Check if user has permission to access this real estate
        $this->_realEstateService->authorizeRealEstateEntityAccess($realEstate);

        // Return all addresses for this real estate
        return $realEstate->addresses()->get();
    }
}
