<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Queries;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\RealEstate\Models\RealEstate;

class RealEstateResolver
{
    /**
     * Resolves and returns a specific real estate entity by its ID or the CNPJ of its associated organization.
     *
     * If neither `id` nor `cnpj` is provided in the arguments, an exception is thrown.
     *
     * @param mixed $root Unused root value from the GraphQL resolver signature.
     * @param array $args Arguments array containing either 'id' or 'cnpj' as identifiers.
     * @return RealEstate The matching real estate entity.
     * @throws \Exception If neither 'id' nor 'cnpj' is provided.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching real estate entity is found.
     */
    public function __invoke($root, array $args): RealEstate
    {
        $query = RealEstate::query()->with('organization');

        if (isset($args['id'])) {
            $query->where('id', $args['id']);
        } elseif (isset($args['cnpj'])) {
            // CNPJ agora está na tabela organizations
            $query->whereHas('organization', function ($q) use ($args) {
                $q->where('cnpj', $args['cnpj']);
            });
        } else {
            throw new \Exception('Você precisa fornecer um ID ou CNPJ para buscar uma imobiliária');
        }

        return $query->firstOrFail();
    }
}
