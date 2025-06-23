<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Models;

use Modules\Organization\Models\Organization;

class RealEstateOrganization extends Organization
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'real_estate_organizations';

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
        'creci',
        'state_registration',
        'legal_representative',
        'active',
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
     * Escopo para filtrar por CRECI
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $creci
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCRECI($query, $creci)
    {
        return $query->where('creci', $creci);
    }
}
