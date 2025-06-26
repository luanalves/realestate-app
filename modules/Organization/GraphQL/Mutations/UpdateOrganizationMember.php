<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;

class UpdateOrganizationMember
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return bool
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
            
            if (isset($args['isActive'])) {
                $updateData['is_active'] = $args['isActive'];
            }
            
            if (!empty($updateData)) {
                $membership->update($updateData);
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating organization member: ' . $e->getMessage(), [
                'exception' => $e,
                'args' => $args,
            ]);
            return false;
        }
    }
}
