<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Modules\Organization\Models\OrganizationAddress;
use Modules\Organization\Services\OrganizationTypeRegistry;

class CreateOrganizationAddress
{
    protected OrganizationTypeRegistry $typeRegistry;

    public function __construct(OrganizationTypeRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return \Modules\Organization\Models\OrganizationAddress
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): OrganizationAddress
    {
        // Resolve organization class based on type
        $organizationClass = $this->typeRegistry->getClass($args['organizationType']);
        
        // Find the organization
        $organization = $organizationClass::findOrFail($args['organizationId']);
        
        // Create the address
        return OrganizationAddress::create([
            'organization_type' => $organizationClass,
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
