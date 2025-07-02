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

class AddOrganizationMember
{
    /**
     * Adds a user as a member to an organization or updates their membership if it already exists.
     *
     * If the user is already a member, updates their role, position (if provided), and activates the membership. If not, creates a new active membership with the specified role and optional position.
     *
     * @param array<string, mixed> $args GraphQL arguments including 'organizationId', 'userId', 'role', and optionally 'position'.
     * @return bool True on successful addition or update, false if an error occurs.
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): bool
    {
        try {
            // Find the organization
            $organization = Organization::findOrFail($args['organizationId']);

            // Find the user
            $user = User::findOrFail($args['userId']);

            // Check if user is already associated with organization
            $existingMembership = OrganizationMembership::where([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ])->first();

            if ($existingMembership) {
                // Update existing membership
                $existingMembership->update([
                    'role' => $args['role'],
                    'position' => $args['position'] ?? $existingMembership->position,
                    'is_active' => true,
                ]);
            } else {
                // Create new membership
                OrganizationMembership::create([
                    'user_id' => $user->id,
                    'organization_id' => $organization->id,
                    'role' => $args['role'],
                    'position' => $args['position'] ?? null,
                    'is_active' => true,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error adding organization member: '.$e->getMessage(), [
                'exception' => $e,
                'args' => $args,
            ]);

            return false;
        }
    }
}
