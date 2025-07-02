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
use Modules\RealEstate\Models\RealEstate;

class UpdateRealEstateResolver
{
    /**
     * Updates an existing real estate entity and its associated organization with the provided input data.
     *
     * Accepts an array of arguments containing the real estate ID and an input array with updated fields. Updates both organization and real estate-specific fields if present, and returns the updated real estate instance.
     *
     * @param null $root Unused parameter.
     * @param array $args Arguments containing 'id' (real estate ID) and 'input' (fields to update).
     * @return RealEstate The updated real estate entity.
     * @throws ModelNotFoundException If the real estate entity with the given ID does not exist.
     */
    public function __invoke($root, array $args): RealEstate
    {
        $id = $args['id'];
        $input = $args['input'];
        
        // Inicia a transação para garantir atomicidade
        return DB::transaction(function () use ($id, $input) {
            // 1. Encontra a imobiliária
            $realEstate = RealEstate::with('organization')->findOrFail($id);
            
            // 2. Atualiza os campos da organização base
            $organization = $realEstate->organization;
            
            if (isset($input['name'])) {
                $organization->name = $input['name'];
            }
            
            if (isset($input['fantasyName'])) {
                $organization->fantasy_name = $input['fantasyName'];
            }
            
            if (isset($input['description'])) {
                $organization->description = $input['description'];
            }
            
            if (isset($input['email'])) {
                $organization->email = $input['email'];
            }
            
            if (isset($input['phone'])) {
                $organization->phone = $input['phone'];
            }
            
            if (isset($input['website'])) {
                $organization->website = $input['website'];
            }
            
            if (isset($input['active'])) {
                $organization->active = $input['active'];
            }
            
            $organization->save();
            
            // 3. Atualiza os campos específicos da imobiliária
            if (isset($input['creci'])) {
                $realEstate->creci = $input['creci'];
            }
            
            if (isset($input['stateRegistration'])) {
                $realEstate->state_registration = $input['stateRegistration'];
            }
            
            $realEstate->save();
            
            return $realEstate;
        });
    }
}
