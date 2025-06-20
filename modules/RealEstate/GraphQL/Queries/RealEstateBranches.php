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
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RealEstateBranches
{
    /**
     * Return all branch addresses for a real estate agency.
     */
    public function __invoke(RealEstate $parent, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Collection
    {
        // Return all branch addresses for this real estate agency
        return $parent->addresses()->where('real_estate_id', $parent->id)->get();
    }
}
