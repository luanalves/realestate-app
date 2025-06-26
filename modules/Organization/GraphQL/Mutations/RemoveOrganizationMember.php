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
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RemoveOrganizationMember
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
            
            // Find and remove the membership
            $membership = OrganizationMembership::where([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ])->first();
            
            if ($membership) {
                // Since we removed SoftDeletes, we'll just delete the record
                $membership->delete();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Error removing organization member: '.$e->getMessage(), [
                'exception' => $e,
                'args' => $args,
            ]);

            return false;
        }
    }
}
