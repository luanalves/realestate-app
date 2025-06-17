<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\Security\Models\LogDetail;
use Modules\Security\Models\SecurityLog as SecurityLogModel;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SecurityLogDetails
{
    /**
     * Return detailed log information from MongoDB.
     */
    public function __invoke(SecurityLogModel $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?array
    {
        // Find the corresponding log detail in MongoDB
        $logDetail = LogDetail::where('security_log_id', $rootValue->id)->first();

        if (!$logDetail) {
            return null;
        }

        $details = $logDetail->details ?? null;

        if (!$details) {
            return null;
        }

        // Convert JSON fields to proper JSON objects for GraphQL response
        return [
            'request' => isset($details['request']) ? [
                'headers' => $details['request']['headers'] ?? null,
                'variables' => $details['request']['variables'] ?? null,
                'query' => $details['request']['query'] ?? null,
                'user_agent' => $details['request']['user_agent'] ?? null,
                'timestamp' => $details['request']['timestamp'] ?? null,
            ] : null,
            'response' => isset($details['response']) ? [
                'status_code' => $details['response']['status_code'] ?? null,
                'headers' => $details['response']['headers'] ?? null,
                'data' => $details['response']['data'] ?? null,
                'size' => $details['response']['size'] ?? null,
            ] : null,
            'execution' => isset($details['execution']) ? [
                'duration_ms' => $details['execution']['duration_ms'] ?? null,
                'memory_peak' => $details['execution']['memory_peak'] ?? null,
                'memory_usage' => $details['execution']['memory_usage'] ?? null,
            ] : null,
        ];
    }
}
