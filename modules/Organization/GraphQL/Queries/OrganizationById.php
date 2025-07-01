<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Queries;

use Modules\Organization\Models\Organization;

class OrganizationById
{
    public function __invoke($root, array $args): ?Organization
    {
        // Validate ID parameter
        if (!isset($args['id']) || !is_numeric($args['id']) || $args['id'] <= 0) {
            throw new \InvalidArgumentException('Invalid organization ID provided');
        }

        try {
            return Organization::find($args['id']);
        } catch (\Exception $e) {
            Log::error('Failed to fetch organization', [
                'id' => $args['id'],
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Unable to fetch organization data');
        }
    }
}
