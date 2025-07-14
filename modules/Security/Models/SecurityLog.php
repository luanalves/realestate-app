<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\Models;

use Modules\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SecurityLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'security_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'email',
        'operation',
        'module',
        'ip_address',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that made the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the detailed log information from MongoDB.
     */
    public function logDetail(): HasOne
    {
        return $this->hasOne(LogDetail::class, 'security_log_id', 'id');
    }
}
