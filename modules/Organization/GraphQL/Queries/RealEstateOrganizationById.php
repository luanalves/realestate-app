<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Modules\Organization\Models\RealEstateOrganization;

class RealEstateOrganizationById
{
    /**
     * @param null $rootValue
     * @param array<string, mixed> $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @return \Modules\Organization\Models\RealEstateOrganization|null
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?RealEstateOrganization
    {
        return RealEstateOrganization::find($args['id']);
    }
}
