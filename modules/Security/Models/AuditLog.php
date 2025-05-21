<?php
/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */
declare(strict_types=1);

namespace Modules\Security\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'graphql_audit_logs';

    protected $fillable = [
        'uuid',
        'user_id',
        'email',
        'operation',
        'module',
        'ip',
        'status',
        'created_at',
    ];

    public $timestamps = false;
}
