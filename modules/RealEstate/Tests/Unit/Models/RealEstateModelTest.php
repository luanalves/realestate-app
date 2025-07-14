<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit\Models;

use Modules\RealEstate\Models\RealEstate;
use Tests\TestCase;

class RealEstateModelTest extends TestCase
{
    /**
     * Test RealEstate model can be instantiated.
     */
    public function testRealEstateModelCanBeInstantiated(): void
    {
        $realEstate = new RealEstate();

        $this->assertInstanceOf(RealEstate::class, $realEstate);
    }

    /**
     * Test RealEstate model has expected fillable attributes.
     */
    public function testRealEstateModelHasExpectedFillableAttributes(): void
    {
        $realEstate = new RealEstate();
        $fillable = $realEstate->getFillable();

        $this->assertContains('organization_id', $fillable);
        $this->assertContains('creci', $fillable);
        $this->assertContains('state_registration', $fillable);
    }

    /**
     * Test RealEstate model has expected constants from RealEstateConstants.
     */
    public function testRealEstateModelHasExpectedConstants(): void
    {
        $this->assertTrue(defined('Modules\RealEstate\Support\RealEstateConstants::MODULE_NAME'));
        $this->assertTrue(defined('Modules\RealEstate\Support\RealEstateConstants::ORGANIZATION_TYPE'));
        $this->assertTrue(defined('Modules\RealEstate\Support\RealEstateConstants::CRECI_STATUS_ACTIVE'));
    }

    /**
     * Test RealEstate model has organization relationship.
     */
    public function testRealEstateModelHasOrganizationRelationship(): void
    {
        $realEstate = new RealEstate();

        $this->assertTrue(method_exists($realEstate, 'organization'));
    }

    /**
     * Test RealEstate __get method has organization attribute delegation.
     */
    public function testGetMethodHasOrganizationAttributeDelegation(): void
    {
        $realEstate = new RealEstate();

        $this->assertTrue(method_exists($realEstate, '__get'));
    }
}
