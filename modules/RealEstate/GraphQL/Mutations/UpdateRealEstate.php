<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateRealEstate
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
     * Update an existing real estate agency.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): RealEstate
    {
        // Extract input data
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];

        // Delegate to service layer for business logic
        return $this->_realEstateService->updateRealEstate($id, $input);
    }
}
