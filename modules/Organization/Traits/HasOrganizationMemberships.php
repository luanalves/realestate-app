<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Traits;

use Modules\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasOrganizationMemberships
{
    /**
     * Defines a many-to-many relationship between the organization and its user members via the `organization_members` pivot table.
     *
     * The relationship includes additional pivot fields: `role`, `position`, and `is_active`, and manages timestamps on the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'organization_members',
            'organization_id',
            'user_id'
        )->withPivot(['role', 'position', 'is_active'])
         ->withTimestamps();
    }
    
    /**
     * Returns the members of the organization who are marked as active.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('is_active', true);
    }
    
    /**
     * Returns organization members with a specific role.
     *
     * @param string $role The role to filter members by.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany The relationship query for members with the given role.
     */
    public function membersWithRole(string $role): BelongsToMany
    {
        return $this->members()->wherePivot('role', $role);
    }
    
    /****
     * Determines whether the given user is a member of the organization.
     *
     * @param User $user The user to check for membership.
     * @return bool True if the user is a member of the organization; otherwise, false.
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('users.id', $user->id)->exists();
    }
}
