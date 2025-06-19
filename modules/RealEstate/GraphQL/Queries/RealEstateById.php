<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RealEstateById
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
     * Return a specific real estate agency by ID.
     *
     * @param mixed $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return RealEstate
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): RealEstate
    {
        $id = (int) $args['id'];
        $realEstate = RealEstate::with('addresses')->findOrFail($id);
        
        // Check if user can access this real estate
        $this->_realEstateService->authorizeRealEstateEntityAccess($realEstate);
        
        return $realEstate;
    }
}
