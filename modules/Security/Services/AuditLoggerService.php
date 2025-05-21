<?php
/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */
declare(strict_types=1);

namespace Modules\Security\Services;

use Modules\Security\Models\AuditLog;
use MongoDB\Client;

class AuditLoggerService
{
    public static function logRequest(array $meta, ?string $operation, $status, array $details): void
    {
        // Persist basic log in PostgreSQL
        AuditLog::create($meta);

        // Persist details in MongoDB
        $client = new Client(env('MONGO_DB_URI', 'mongodb://mongo:27017'));
        $collection = $client->selectCollection(env('MONGO_DB_DATABASE', 'audit'), 'graphql_audit_details');
        $collection->insertOne($details);
    }
}
