<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\Organization\Models\OrganizationAddress;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteOrganizationAddress
{
    /**
     * Deletes an organization address by its ID and returns the deleted address data.
     *
     * If the address does not exist, a GraphQL error is thrown. The returned model contains the data of the deleted address for use in the GraphQL response.
     *
     * @param array<string, mixed> $args Arguments containing the 'id' of the address to delete.
     * @return OrganizationAddress The deleted organization address as a model instance.
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): OrganizationAddress
    {
        $address = OrganizationAddress::find($args['id']);
        if (!$address) {
            throw new \GraphQL\Error\Error('Organization address with ID '.$args['id'].' does not exist.');
        }
        $addressData = $address->toArray();
        $address->delete();
        // Rehydrate the model for GraphQL response
        $deletedAddress = new OrganizationAddress();
        $deletedAddress->setRawAttributes($addressData, true);
        $deletedAddress->exists = true;

        return $deletedAddress;
    }
}
