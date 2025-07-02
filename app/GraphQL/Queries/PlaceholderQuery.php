<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace App\GraphQL\Queries;

class PlaceholderQuery
{
    /**
     * Acts as a placeholder resolver for the base GraphQL Query type, returning null for all invocations.
     *
     * @return null Always returns null.
     */
    public function __invoke($root, array $args)
    {
        return null;
    }
}
