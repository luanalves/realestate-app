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

class RealEstateAddress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'real_estate_addresses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'real_estate_id',
        'type',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'country',
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
     * Get the real estate agency that owns this address.
     */
    public function realEstate(): BelongsTo
    {
        return $this->belongsTo(RealEstate::class);
    }

    /**
     * Scope a query to only include active addresses.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include headquarters addresses.
     */
    public function scopeHeadquarters($query)
    {
        return $query->where('type', 'headquarters');
    }

    /**
     * Scope a query to only include branch addresses.
     */
    public function scopeBranches($query)
    {
        return $query->where('type', 'branch');
    }
}
