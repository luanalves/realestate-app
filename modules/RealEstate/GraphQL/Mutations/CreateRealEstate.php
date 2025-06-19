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

class CreateRealEstate
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
     * Create a new real estate agency.
     *
     * @param mixed $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return RealEstate
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): RealEstate
    {
        // Delegate to service layer for business logic
        return $this->_realEstateService->createRealEstate($args);
    }
}
