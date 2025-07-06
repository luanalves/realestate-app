<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Organization\Models\Organization;
use Modules\RealEstate\Support\RealEstateConstants;

class RealEstate extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'real_estates';

    /**
     * Indica se o Eloquent deve gerenciar as timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id', // Referência à ID da organização relacionada
        'creci',
        'state_registration',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relação com a organização base.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Escopo para filtrar por CRECI.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $creci
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCRECI($query, $creci)
    {
        return $query->where('creci', $creci);
    }

    /**
     * Método estático que retorna o tipo de organização.
     */
    public static function getOrganizationType(): string
    {
        return RealEstateConstants::ORGANIZATION_TYPE;
    }

    /**
     * Delegate organization attributes to the organization model.
     * This allows accessing organization fields directly on the RealEstate model.
     */
    public function __get($key)
    {
        // First check if the attribute exists on this model
        if (array_key_exists($key, $this->attributes) || $this->hasGetMutator($key)) {
            return parent::__get($key);
        }

        // If the organization is loaded and has the attribute, delegate to it
        if ($this->relationLoaded('organization') && 
            $this->organization && 
            $this->organization->hasAttribute($key)) {
            return $this->organization->getAttribute($key);
        }

        // For the most common organization attributes, try to load the organization
        $organizationAttributes = ['name', 'fantasy_name', 'cnpj', 'description', 'email', 'phone', 'website', 'active'];
        if (in_array($key, $organizationAttributes) && !$this->relationLoaded('organization')) {
            $this->load('organization');
            if ($this->organization && $this->organization->hasAttribute($key)) {
                return $this->organization->getAttribute($key);
            }
        }

        return parent::__get($key);
    }

    /**
     * Check if an attribute exists on this model or the organization.
     */
    public function hasAttribute($key): bool
    {
        return array_key_exists($key, $this->attributes) || 
               ($this->relationLoaded('organization') && 
                $this->organization && 
                $this->organization->hasAttribute($key));
    }
}
