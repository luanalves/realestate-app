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
     * Updates an existing organization address with the provided fields.
     *
     * Accepts an address ID and a set of address-related fields to update. Only fields present in the input are updated. Returns the updated `OrganizationAddress` model instance.
     *
     * @param null $_ Unused placeholder parameter.
     * @param array<string, mixed> $args Input arguments, including the address ID and fields to update.
     * @return \Modules\Organization\Models\OrganizationAddress The updated organization address instance.
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
