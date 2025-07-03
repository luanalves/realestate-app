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
use Modules\RealEstate\Services\RealEstateService;
use Modules\RealEstate\Support\RealEstateConstants;

class CreateRealEstateResolver
{
    /**
     * @var RealEstateService
     */
    protected $realEstateService;

    /**
     * CreateRealEstateResolver constructor.
     * 
     * @param RealEstateService $realEstateService
     */
    public function __construct(RealEstateService $realEstateService)
    {
        $this->realEstateService = $realEstateService;
    }

    /**
     * Creates a new real estate organization.
     *
     * @param null $root
     * @param array $args
     * @return RealEstate
     */
    public function __invoke($root, array $args): RealEstate
    {
        return $this->realEstateService->createRealEstate($args['input']);
    }
}
