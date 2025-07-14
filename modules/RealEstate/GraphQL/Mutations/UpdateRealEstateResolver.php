<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\GraphQL\Mutations;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;

class UpdateRealEstateResolver
{
    /**
     * @var RealEstateService
     */
    protected $realEstateService;

    /**
     * UpdateRealEstateResolver constructor.
     */
    public function __construct(RealEstateService $realEstateService)
    {
        $this->realEstateService = $realEstateService;
    }

    /**
     * Atualiza uma imobiliária existente.
     *
     * @param null $root
     *
     * @throws ModelNotFoundException
     */
    public function __invoke($root, array $args): RealEstate
    {
        // Extract id from args
        $id = (int) $args['id'];

        // Remove id from args to get only input data
        unset($args['id']);

        // Pass the remaining args as input data (because of @spread directive)
        return $this->realEstateService->updateRealEstate($id, $args);
    }
}
