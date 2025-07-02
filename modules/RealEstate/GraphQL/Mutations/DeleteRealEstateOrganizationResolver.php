<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Mutations;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Models\RealEstateOrganization;

class DeleteRealEstateOrganizationResolver
{
    /**
     * Deletes a real estate organization and its associated base organization atomically.
     *
     * Expects an array with an 'id' key specifying the real estate organization to delete. The operation is performed within a database transaction to ensure atomicity. Returns a copy of the deleted real estate organization.
     *
     * @param null $root
     * @param array $args Arguments containing the 'id' of the real estate organization to delete.
     * @return RealEstateOrganization The deleted real estate organization instance.
     * @throws ModelNotFoundException If the specified real estate organization is not found.
     */
    public function __invoke($root, array $args): RealEstateOrganization
    {
        $id = $args['id'];
        
        // Inicia a transação para garantir atomicidade
        return DB::transaction(function () use ($id) {
            // 1. Encontra a imobiliária com sua organização base
            $realEstate = RealEstateOrganization::with('organization')->findOrFail($id);
            
            // 2. Guarda uma cópia para retornar após a exclusão
            $returnCopy = clone $realEstate;
            
            // 3. Exclui a imobiliária (a organização base será excluída em cascata devido à foreign key)
            $realEstate->organization->delete();
            
            return $returnCopy;
        });
    }
}
