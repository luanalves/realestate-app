<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasOrganizationMemberships
{
    /**
     * Relação com os membros da organização
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function members(): MorphToMany
    {
        return $this->morphToMany(
            User::class,
            'organization',
            'organization_memberships',
            'organization_id',
            'user_id'
        )->withPivot(['role', 'position', 'is_active', 'joined_at'])
         ->withTimestamps();
    }
    
    /**
     * Retorna apenas membros ativos desta organização
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function activeMembers(): MorphToMany
    {
        return $this->members()->wherePivot('is_active', true);
    }
    
    /**
     * Retorna membros com um papel específico
     *
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function membersWithRole(string $role): MorphToMany
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
