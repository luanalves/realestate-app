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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\RealEstate\Models\RealEstateAddress;

class RealEstate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'real_estates';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'fantasy_name',
        'corporate_name',
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
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the addresses for this real estate agency.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(RealEstateAddress::class);
    }

    /**
     * Get the headquarters address for this real estate agency.
     */
    public function headquarters()
    {
        return $this->hasOne(RealEstateAddress::class)->where('type', 'headquarters');
    }

    /**
     * Get the branch addresses for this real estate agency.
     */
    public function branches(): HasMany
    {
        return $this->hasMany(RealEstateAddress::class)->where('type', 'branch');
    }

    /**
     * Scope a query to only include active real estates.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to filter by tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
