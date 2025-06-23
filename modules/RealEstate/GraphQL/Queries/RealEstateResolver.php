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
     * Obtém uma imobiliária específica pelo ID ou CNPJ.
     *
     * @param null $root
     *
     * @throws ModelNotFoundException
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
