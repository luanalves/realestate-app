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
    public $timestamps = false;

    /**
     * Define a chave primária (foreign key referenciando organizations).
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * A chave primária não é auto-incremento.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id', // Referência à ID da organização pai
        'creci',
        'state_registration',
    ];

    /**
     * Relação com a organização base.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'id');
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
     * Acessa o nome da organização.
     */
    public function getNameAttribute(): ?string
    {
        return $this->organization?->name;
    }

    /**
     * Acessa a descrição da organização.
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->organization?->description;
    }

    /**
     * Acessa o email da organização.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->organization?->email;
    }

    /**
     * Acessa o nome fantasia da organização.
     */
    public function getFantasyNameAttribute(): ?string
    {
        return $this->organization?->fantasy_name;
    }

    /**
     * Acessa o CNPJ da organização.
     */
    public function getCnpjAttribute(): ?string
    {
        return $this->organization?->cnpj;
    }

    /**
     * Acessa o telefone da organização.
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->organization?->phone;
    }

    /**
     * Acessa o website da organização.
     */
    public function getWebsiteAttribute(): ?string
    {
        return $this->organization?->website;
    }

    /**
     * Acessa o status ativo da organização.
     */
    public function getActiveAttribute(): bool
    {
        return $this->organization?->active ?? false;
    }

    /**
     * Acessa a data de criação da organização.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getCreatedAtAttribute()
    {
        return $this->organization?->created_at;
    }

    /**
     * Acessa a data de atualização da organização.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getUpdatedAtAttribute()
    {
        return $this->organization?->updated_at;
    }
}
