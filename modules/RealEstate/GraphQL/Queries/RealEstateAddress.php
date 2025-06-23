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
use Modules\RealEstate\Models\RealEstateAddress as RealEstateAddressModel;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RealEstateAddress
{
    /**
     * Return all addresses for a real estate agency.
     */
    public function __invoke(RealEstate $parent, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): RealEstateAddressModel
    {
        return $parent->addresses()->first();
    }
}
