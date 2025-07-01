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
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Support\RealEstateConstants;

class CreateRealEstateResolver
{
    /**
     * /**
     * Creates a new real estate organization.
     *
     * @param null $root
     */
    public function __invoke($root, array $args): RealEstate
    {
        $input = $args['input'];

        return DB::transaction(function () use ($input) {
            // 1. Create the base organization first
            $organization = new Organization();
            $organization->name = $input['name'];
            $organization->fantasy_name = $input['fantasyName'] ?? null;
            $organization->cnpj = $input['cnpj'];
            $organization->description = $input['description'] ?? null;
            $organization->email = $input['email'];
            $organization->phone = $input['phone'] ?? null;
            $organization->website = $input['website'] ?? null;
            $organization->active = $input['active'] ?? true;
            $organization->organization_type = RealEstateConstants::ORGANIZATION_TYPE;
            $organization->save();

            // 2. Create the real estate entity using the base organization ID
            $realEstate = new RealEstate();
            $realEstate->id = $organization->id;
            $realEstate->creci = $input['creci'] ?? null;
            $realEstate->state_registration = $input['stateRegistration'] ?? null;
            $realEstate->save();

            // 3. Load the relationship to ensure we have all the data
            $realEstate->load('organization');
            $realEstate->load('organization');

            return $realEstate;
        });
    }
}
