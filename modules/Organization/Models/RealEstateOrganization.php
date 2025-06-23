<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Models;

/**
 * Modelo específico para imobiliárias
 */
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
}
