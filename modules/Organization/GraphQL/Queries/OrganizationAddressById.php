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
use Modules\Organization\Models\OrganizationAddress;

class OrganizationAddressById
{
    /**
     * Retrieves an OrganizationAddress model by its ID.
     *
     * Returns the OrganizationAddress instance matching the provided 'id' argument, or null if not found.
     *
     * @param array<string, mixed> $args Arguments containing the 'id' of the address to retrieve.
     * @return \Modules\Organization\Models\OrganizationAddress|null The matching OrganizationAddress instance, or null if not found.
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?OrganizationAddress
    {
        return OrganizationAddress::find($args['id']);
    }
}
