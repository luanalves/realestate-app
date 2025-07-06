<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit\GraphQL;

use Modules\RealEstate\GraphQL\Queries\RealEstateFieldResolvers;
use Tests\TestCase;

class RealEstateFieldResolversTest extends TestCase
{
    /**
     * Test RealEstateFieldResolvers can be instantiated.
     */
    public function testRealEstateFieldResolversCanBeInstantiated(): void
    {
        $resolver = new RealEstateFieldResolvers();

        $this->assertInstanceOf(RealEstateFieldResolvers::class, $resolver);
    }

    /**
     * Test that all expected methods exist.
     */
    public function testAllExpectedMethodsExist(): void
    {
        $resolver = new RealEstateFieldResolvers();

        $this->assertTrue(method_exists($resolver, 'memberships'));
        $this->assertTrue(method_exists($resolver, 'members'));
        $this->assertTrue(method_exists($resolver, 'activeMembers'));
        $this->assertTrue(method_exists($resolver, 'mainAddress'));
        $this->assertTrue(method_exists($resolver, 'addresses'));
    }
}
