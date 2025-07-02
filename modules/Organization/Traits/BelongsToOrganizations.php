<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Organization\Models\OrganizationMembership;

trait BelongsToOrganizations
{
    /**
     * Returns a HasMany relationship for all organization memberships associated with the user.
     *
     * @return HasMany The Eloquent relationship representing all organization memberships for the user.
     */
    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }
    
    /**
     * Returns a relationship for all active organization memberships associated with the user.
     *
     * Only memberships where the `is_active` attribute is true are included.
     *
     * @return HasMany
     */
    public function activeOrganizationMemberships(): HasMany
    {
        return $this->organizationMemberships()->where('is_active', true);
    }
    
    /**
     * Returns the organization memberships for this user that match the specified role.
     *
     * @param string $role The role to filter organization memberships by.
     * @return HasMany The filtered organization memberships relationship.
     */
    public function organizationMembershipsWithRole(string $role): HasMany
    {
        return $this->organizationMemberships()->where('role', $role);
    }
}
