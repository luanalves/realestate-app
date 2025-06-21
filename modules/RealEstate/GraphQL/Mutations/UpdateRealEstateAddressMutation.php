<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Modules\RealEstate\Services\RealEstateService;

class UpdateRealEstateAddressMutation
{
    protected RealEstateService $realEstateService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(RealEstateService $realEstateService)
    {
        $this->realEstateService = $realEstateService;
    }

    /**
     * Update an existing real estate address
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $addressId = (int)$args['id'];
        $addressData = $args['input'] ?? [];
        
        // Update the address using the service
        return $this->realEstateService->updateRealEstateAddressById(
            $addressId,
            $addressData,
            $context->user()
        );
    }
}
