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
     * Defines the relationship to the associated Organization model using the 'id' foreign key.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'id');
    }

    /**
     * Adds a query scope to filter real estate records by the specified CRECI value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance.
     * @param string $creci The CRECI registration number to filter by.
     * @return \Illuminate\Database\Eloquent\Builder The modified query builder.
     */
    public function scopeWhereCRECI($query, $creci)
    {
        return $query->where('creci', $creci);
    }

    /**
     * Returns the organization type constant for real estate entities.
     *
     * @return string The organization type identifier defined in RealEstateConstants.
     */
    public static function getOrganizationType(): string
    {
        return RealEstateConstants::ORGANIZATION_TYPE;
    }

    /**
     * Retrieves the name of the related organization.
     *
     * @return string|null The organization's name, or null if unavailable.
     */
    public function getNameAttribute(): ?string
    {
        return $this->organization?->name;
    }

    /**
     * Retrieves the description of the related organization.
     *
     * @return string|null The organization's description, or null if unavailable.
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->organization?->description;
    }

    /**
     * Retrieves the email address from the related organization.
     *
     * @return string|null The organization's email, or null if unavailable.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->organization?->email;
    }

    /**
     * Retrieves the fantasy name of the related organization.
     *
     * @return string|null The organization's fantasy name, or null if unavailable.
     */
    public function getFantasyNameAttribute(): ?string
    {
        return $this->organization?->fantasy_name;
    }

    /**
     * Retrieves the CNPJ of the related organization.
     *
     * @return string|null The organization's CNPJ, or null if unavailable.
     */
    public function getCnpjAttribute(): ?string
    {
        return $this->organization?->cnpj;
    }

    /**
     * Retrieves the phone number from the related organization.
     *
     * @return string|null The organization's phone number, or null if unavailable.
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->organization?->phone;
    }

    /**
     * Retrieves the website URL from the related organization.
     *
     * @return string|null The organization's website URL, or null if unavailable.
     */
    public function getWebsiteAttribute(): ?string
    {
        return $this->organization?->website;
    }

    /**
     * Returns the active status of the related organization.
     *
     * @return bool True if the organization is active; otherwise, false.
     */
    public function getActiveAttribute(): bool
    {
        return $this->organization?->active ?? false;
    }

    /**
     * Retrieves the creation timestamp of the related organization.
     *
     * @return \Illuminate\Support\Carbon|null The organization's creation date, or null if unavailable.
     */
    public function getCreatedAtAttribute()
    {
        return $this->organization?->created_at;
    }

    /**
     * Retrieves the updated timestamp of the related organization.
     *
     * @return \Illuminate\Support\Carbon|null The organization's last update time, or null if unavailable.
     */
    public function getUpdatedAtAttribute()
    {
        return $this->organization?->updated_at;
    }
}
