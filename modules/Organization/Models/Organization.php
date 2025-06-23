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
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Organization\Traits\HasOrganizationMemberships;

/**
 * Modelo base para todos os tipos de organizações no sistema
 */
abstract class Organization extends Model
{
    use HasFactory, SoftDeletes, HasOrganizationMemberships;

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
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtém os endereços desta organização
     *
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(OrganizationAddress::class);
    }

    /**
     * Escopo para filtrar apenas organizações ativas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
