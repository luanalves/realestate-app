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
     * Get all organization memberships for this user
     *
     * @return HasMany
     */
    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }
    
    /**
     * Get active organization memberships for this user
     *
     * @return HasMany
     */
    public function activeOrganizationMemberships(): HasMany
    {
        return $this->organizationMemberships()->where('is_active', true);
    }
    
    /**
     * Get organization memberships for this user with a specific role
     *
     * @param string $role
     * @return HasMany
     */
    public function organizationMembershipsWithRole(string $role): HasMany
    {
        return $this->organizationMemberships()->where('role', $role);
    }
    
    /**
     * Get specific type of organizations for this user
     * 
     * @param string $organizationType Full class name of the organization model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrganizationsOfType(string $organizationType)
    {
        return $this->organizationMemberships()
            ->where('organization_type', $organizationType)
            ->get()
            ->map(function ($membership) {
                return $membership->organization;
            });
    }
}
