<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit\Services;

use Modules\RealEstate\Services\RealEstateService;
use Tests\TestCase;

class RealEstateServiceExtendedTest extends TestCase
{
    /**
     * @var RealEstateService
     */
    private $service;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RealEstateService();
    }

    /**
     * Test RealEstateService can be instantiated.
     */
    public function testRealEstateServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(RealEstateService::class, $this->service);
    }

    /**
     * Test RealEstateService has expected methods.
     */
    public function testRealEstateServiceHasExpectedMethods(): void
    {
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateAccess'));
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateWrite'));
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateEntityAccess'));
        $this->assertTrue(method_exists($this->service, 'updateRealEstate'));
        $this->assertTrue(method_exists($this->service, 'deleteRealEstate'));
        $this->assertTrue(method_exists($this->service, 'getRealEstateById'));
        // Note: createRealEstate method was removed as creation is delegated to Organization module
    }

    /**
     * Test service methods exist and are callable.
     */
    public function testServiceMethodsExistAndCallable(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $expectedMethods = [
            'authorizeRealEstateAccess',
            'authorizeRealEstateWrite',
            'authorizeRealEstateEntityAccess',
            'updateRealEstate',
            'deleteRealEstate',
            'getRealEstateById',
            // Note: createRealEstate method removed as creation is delegated to Organization module
        ];

        $actualMethods = array_map(function ($method) {
            return $method->getName();
        }, $methods);

        foreach ($expectedMethods as $expectedMethod) {
            $this->assertContains($expectedMethod, $actualMethods, "Method $expectedMethod not found");
        }
    }

    /**
     * Test service structure after address delegation to Organization module.
     */
    public function testServiceStructureAfterAddressDelegation(): void
    {
        $reflection = new \ReflectionClass($this->service);

        // These methods should not exist anymore
        $this->assertFalse($reflection->hasMethod('createRealEstateAddress'));
        $this->assertFalse($reflection->hasMethod('updateRealEstateAddress'));
        $this->assertFalse($reflection->hasMethod('createRealEstateAddressForExisting'));
        $this->assertFalse($reflection->hasMethod('updateRealEstateAddressById'));
        $this->assertFalse($reflection->hasMethod('deleteRealEstateAddress'));

        // Core methods should still exist
        $this->assertTrue($reflection->hasMethod('updateRealEstate'));
        $this->assertTrue($reflection->hasMethod('deleteRealEstate'));
        $this->assertTrue($reflection->hasMethod('getRealEstateById'));

        // This method should not exist anymore
        $this->assertFalse($reflection->hasMethod('createRealEstate'));
    }
}
