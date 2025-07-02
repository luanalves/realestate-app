<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace App\GraphQL\Mutations;

class PlaceholderMutation
{
    /**
     * Acts as a placeholder resolver for the base GraphQL Mutation type.
     *
     * Always returns null and performs no operation.
     *
     * @return null
     */
    public function __invoke($root, array $args)
    {
        return null;
    }
}
