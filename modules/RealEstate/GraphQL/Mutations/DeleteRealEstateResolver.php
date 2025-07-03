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

class DeleteRealEstateResolver
{
    /**
     * @var RealEstateService
     */
    protected $realEstateService;

    /**
     * DeleteRealEstateResolver constructor.
     * 
     * @param RealEstateService $realEstateService
     */
    public function __construct(RealEstateService $realEstateService)
    {
        $this->realEstateService = $realEstateService;
    }

    /**
     * Exclui uma imobiliária existente
     *
     * @param null $root
     * @param array $args
     * @return RealEstate
     * @throws ModelNotFoundException
     */
    public function __invoke($root, array $args): RealEstate
    {
        return $this->realEstateService->deleteRealEstate((int) $args['id']);
    }
}
