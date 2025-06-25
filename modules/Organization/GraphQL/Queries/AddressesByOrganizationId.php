<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationAddress;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AddressesByOrganizationId
{
    /**
     * @param null                 $_
     * @param array<string, mixed> $args
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $organizationClass = Organization::class;

            // Check if organization exists
            $organization = $organizationClass::find($args['organizationId']);
            if (!$organization) {
                return collect();
            }

            return OrganizationAddress::where([
                'organization_id' => $args['organizationId'],
            ])->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching organization addresses: '.$e->getMessage(), [
                'exception' => $e,
                'args' => $args,
            ]);

            return collect();
        }
    }
}
