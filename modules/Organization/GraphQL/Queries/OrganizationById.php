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
        return Organization::find($args['id']);
    }
}
