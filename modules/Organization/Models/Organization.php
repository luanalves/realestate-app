<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Organization\Traits\HasOrganizationMemberships;

/**
 * Modelo base para todos os tipos de organizações no sistema.
 *
 * Este modelo representa dados genéricos de organizações.
 * Tipos específicos como RealEstate devem usar este modelo como base
 * através de relacionamentos ou herança.
 */
class Organization extends Model
{
    use HasFactory;
    use HasOrganizationMemberships;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'fantasy_name',
        'cnpj',
        'description',
        'email',
        'phone',
        'website',
        'active',
        'organization_type',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Memberships da organização.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(
            OrganizationMembership::class,
            'organization_id'
        )->where('organization_type', static::class);
    }

    /**
     * Escopo para filtrar por tipo de organização.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('organization_type', $type);
    }

    /**
     * Escopo para filtrar por organizações ativas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
