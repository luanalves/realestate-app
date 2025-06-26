<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use GraphQL\Error\Error as GraphQLError;
use GraphQL\Type\Definition\ResolveInfo;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationAddress;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateOrganizationAddress
{
    /**
     * @param null                 $_
     * @param array<string, mixed> $args
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): OrganizationAddress
    {
        // Find the organization
        $organization = Organization::find($args['organizationId']);
        if (!$organization) {
            throw new GraphQLError('Organization with ID '.$args['organizationId'].' does not exist.');
        }

        // Create the address
        return OrganizationAddress::create([
            'organization_id' => $organization->id,
            'type' => $args['type'],
            'street' => $args['street'],
            'number' => $args['number'] ?? null,
            'complement' => $args['complement'] ?? null,
            'neighborhood' => $args['neighborhood'],
            'city' => $args['city'],
            'state' => $args['state'],
            'zip_code' => $args['zipCode'],
            'country' => $args['country'],
            'active' => true,
        ]);
    }
}
