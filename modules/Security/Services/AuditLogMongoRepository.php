<?php
/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */
declare(strict_types=1);

namespace Modules\Security\Services;

use MongoDB\Client;

class AuditLogMongoRepository
{
    protected Client $_client;
    protected string $_collection;

    public function __construct(Client $client, string $collection = 'graphql_audit_details')
    {
        $this->_client = $client;
        $this->_collection = $collection;
    }

    public function insert(array $data): void
    {
        $this->_client->selectCollection(env('MONGO_DB_DATABASE', 'audit'), $this->_collection)
            ->insertOne($data);
    }

    public function findByUuid(string $uuid): ?array
    {
        $doc = $this->_client->selectCollection(env('MONGO_DB_DATABASE', 'audit'), $this->_collection)
            ->findOne(['uuid' => $uuid]);
        return $doc ? $doc->getArrayCopy() : null;
    }
}
