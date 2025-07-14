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

class UpdateOrganizationAddress
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return \Modules\Organization\Models\OrganizationAddress
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): OrganizationAddress
    {
        $address = OrganizationAddress::findOrFail($args['id']);
        
        // Prepare update data
        $updateData = [];
        
        $fields = [
            'type', 'street', 'number', 'complement', 'neighborhood',
            'city', 'state', 'zipCode', 'country', 'active'
        ];
        
        foreach ($fields as $field) {
            if (isset($args[$field])) {
                $columnName = $field === 'zipCode' ? 'zip_code' : $field;
                $updateData[$columnName] = $args[$field];
            }
        }
        
        if (!empty($updateData)) {
            $address->update($updateData);
        }
        
        return $address->fresh();
    }
}
