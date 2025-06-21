<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Modules\RealEstate\Services\RealEstateService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateRealEstateAddressMutation
{
    protected RealEstateService $realEstateService;

    /**
     * Constructor with dependency injection.
     */
    public function __construct(RealEstateService $realEstateService)
    {
        $this->realEstateService = $realEstateService;
    }

    /**
     * Create a new real estate address.
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $realEstateId = (int) $args['realEstateId'];
        
        // Garantir que temos o input e seus campos
        if (!isset($args['input']) || !is_array($args['input'])) {
            throw new \Exception('Input de endereço inválido');
        }
        
        // Logging para debug
        \Log::debug('CreateRealEstateAddressMutation: argumentos recebidos', [
            'realEstateId' => $realEstateId,
            'input' => $args['input']
        ]);
        
        $addressData = $args['input'];

        // Create the address using the service
        return $this->realEstateService->createRealEstateAddressForExisting(
            $realEstateId,
            $addressData,
            $context->user()
        );
    }
}
