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
     * Relação com os membros da organização
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
     * Retorna apenas membros ativos desta organização
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('is_active', true);
    }
    
    /**
     * Retorna membros com um papel específico
     *
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function membersWithRole(string $role): BelongsToMany
    {
        return $this->members()->wherePivot('role', $role);
    }
    
    /**
     * Verifica se um usuário é membro desta organização
     *
     * @param User $user
     * @return bool
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('users.id', $user->id)->exists();
    }
}
