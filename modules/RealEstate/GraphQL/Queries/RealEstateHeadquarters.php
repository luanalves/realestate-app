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
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RealEstateHeadquarters
{
    /**
     * Returns the headquarters address of a real estate agency.
     */
    public function __invoke(RealEstate $parent, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $parent->addresses()->where('real_estate_id', $parent->id)->first();
    }
}
