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
use Modules\Organization\Services\OrganizationTypeRegistry;

class AddressesByOrganizationId
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            // Resolve organization class based on type
            $organizationClass = $this->typeRegistry->getClass($args['organizationType']);
            
            // Check if organization exists
            $organization = $organizationClass::find($args['organizationId']);
            if (!$organization) {
                return collect();
            }

            return OrganizationAddress::where([
                'organization_type' => $organizationClass,
                'organization_id' => $args['organizationId'],
            ])->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching organization addresses: ' . $e->getMessage(), [
                'exception' => $e,
                'args' => $args,
            ]);
            
            return collect();
        }
    }
}
