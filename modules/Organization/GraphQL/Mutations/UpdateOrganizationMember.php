<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use Modules\UserManagement\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateOrganizationMember
{
    /**
     * @param null                 $_
     * @param array<string, mixed> $args
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): bool
    {
        try {
            // Find the organization
            $organization = Organization::findOrFail($args['organizationId']);

            // Find the user
            $user = User::findOrFail($args['userId']);

            // Find the membership
            $membership = OrganizationMembership::where([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ])->first();

            if (!$membership) {
                return false;
            }

            // Prepare update data
            $updateData = [];

            if (isset($args['role'])) {
                $updateData['role'] = $args['role'];
            }

            if (isset($args['position'])) {
                $updateData['position'] = $args['position'];
            }

            if (isset($args['is_active'])) {
                $updateData['is_active'] = $args['is_active'];
            }

            if (!empty($updateData)) {
                $membership->update($updateData);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating organization member: '.$e->getMessage(), [
                'exception' => $e,
                'args' => $args,
            ]);

            return false;
        }
    }
}
