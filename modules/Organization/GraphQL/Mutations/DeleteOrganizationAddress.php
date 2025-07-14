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
     * @param null                 $_
     * @param array<string, mixed> $args
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
