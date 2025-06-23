<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Mutations;

use Illuminate\Support\Facades\DB;
use Modules\Organization\Models\Organization;
use Modules\Organization\Support\OrganizationConstants;
use Modules\RealEstate\Models\RealEstate;

class CreateRealEstateResolver
{
    /**
     * Cria uma nova organização do tipo Imobiliária
     *
     * @param null $root
     * @param array $args
     * @return RealEstate
     */
    public function __invoke($root, array $args): RealEstate
    {
        $input = $args['input'];
        
        // Inicia a transação para garantir atomicidade
        return DB::transaction(function () use ($input) {
            // 1. Cria a organização base primeiro
            $organization = new Organization();
            $organization->name = $input['name'];
            $organization->fantasy_name = $input['fantasyName'] ?? null;
            $organization->cnpj = $input['cnpj'];
            $organization->description = $input['description'] ?? null;
            $organization->email = $input['email'];
            $organization->phone = $input['phone'] ?? null;
            $organization->website = $input['website'] ?? null;
            $organization->active = $input['active'] ?? true;
            $organization->organization_type = OrganizationConstants::ORGANIZATION_TYPE_REAL_ESTATE;
            $organization->save();
            
            // 2. Cria a imobiliária usando o ID da organização base
            $realEstate = new RealEstate();
            $realEstate->id = $organization->id;
            $realEstate->creci = $input['creci'] ?? null;
            $realEstate->state_registration = $input['stateRegistration'] ?? null;
            $realEstate->save();
            
            // 3. Carrega o relacionamento para garantir que temos todos os dados
            $realEstate->load('organization');
            
            return $realEstate;
        });
    }
}
