<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Modules\Organization\Models\RealEstateOrganization;
use Modules\Organization\Models\OrganizationAddress;
use Modules\Organization\Support\OrganizationConstants;

class CreateRealEstateOrganization
{
    /**
     * @param null $rootValue
     * @param array<string, mixed> $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @return \Modules\Organization\Models\RealEstateOrganization
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): RealEstateOrganization
    {
        // Usar transação para garantir integridade dos dados
        return DB::transaction(function () use ($args) {
            // Criar a imobiliária
            $realEstate = RealEstateOrganization::create([
                'name' => $args['name'],
                'fantasy_name' => $args['fantasyName'] ?? null,
                'cnpj' => $args['cnpj'],
                'description' => $args['description'] ?? null,
                'email' => $args['email'],
                'phone' => $args['phone'] ?? null,
                'website' => $args['website'] ?? null,
                'creci' => $args['creci'] ?? null,
                'state_registration' => $args['stateRegistration'] ?? null,
                'legal_representative' => $args['legalRepresentative'] ?? null,
                'active' => $args['active'] ?? true,
            ]);
            
            // Se um endereço foi fornecido, criar também
            if (isset($args['address'])) {
                $address = $args['address'];
                
                $isHeadquarters = ($address['type'] ?? OrganizationConstants::ADDRESS_TYPE_BRANCH) === 
                                  OrganizationConstants::ADDRESS_TYPE_HEADQUARTERS;
                
                // Se o tipo do endereço é matriz e não existem outros endereços,
                // garantir que é uma matriz, caso contrário, garantir que é uma filial
                $addressType = $isHeadquarters ? 
                               OrganizationConstants::ADDRESS_TYPE_HEADQUARTERS : 
                               OrganizationConstants::ADDRESS_TYPE_BRANCH;
                
                OrganizationAddress::create([
                    'organization_id' => $realEstate->id,
                    'organization_type' => get_class($realEstate),
                    'type' => $addressType,
                    'street' => $address['street'],
                    'number' => $address['number'] ?? null,
                    'complement' => $address['complement'] ?? null,
                    'neighborhood' => $address['neighborhood'],
                    'city' => $address['city'],
                    'state' => $address['state'],
                    'zip_code' => $address['zipCode'],
                    'country' => $address['country'] ?? 'BR',
                    'active' => true,
                ]);
            }
            
            return $realEstate;
        });
    }
}
