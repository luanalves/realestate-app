<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\RealEstate\Models\RealEstateAddress;
use Modules\RealEstate\Services\RealEstateService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RealEstateAddressById
{
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
     * Get a specific real estate address by ID.
     *
     * @param mixed $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return RealEstateAddress|null
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?RealEstateAddress
    {
        $address = RealEstateAddress::find($args['id']);
        
        if ($address) {
            // Check if user has permission to access this address
            $this->_realEstateService->authorizeRealEstateEntityAccess($address->realEstate);
        }
        
        return $address;
    }
}
