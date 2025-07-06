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
        $this->assertTrue(method_exists($this->service, 'createRealEstate'));
        $this->assertTrue(method_exists($this->service, 'updateRealEstate'));
        $this->assertTrue(method_exists($this->service, 'deleteRealEstate'));
        $this->assertTrue(method_exists($this->service, 'getRealEstateById'));
        $this->assertTrue(method_exists($this->service, 'createRealEstateAddressForExisting'));
        $this->assertTrue(method_exists($this->service, 'updateRealEstateAddressById'));
        $this->assertTrue(method_exists($this->service, 'deleteRealEstateAddress'));
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
            'createRealEstate',
            'updateRealEstate',
            'deleteRealEstate',
            'getRealEstateById',
            'createRealEstateAddressForExisting',
            'updateRealEstateAddressById',
            'deleteRealEstateAddress'
        ];
        
        $actualMethods = array_map(function($method) {
            return $method->getName();
        }, $methods);
        
        foreach ($expectedMethods as $expectedMethod) {
            $this->assertContains($expectedMethod, $actualMethods, "Method $expectedMethod not found");
        }
    }

    /**
     * Test service has private methods for address handling.
     */
    public function testServiceHasPrivateMethodsForAddressHandling(): void
    {
        $reflection = new \ReflectionClass($this->service);
        
        $this->assertTrue($reflection->hasMethod('createRealEstateAddress'));
        $this->assertTrue($reflection->hasMethod('updateRealEstateAddress'));
        
        $createMethod = $reflection->getMethod('createRealEstateAddress');
        $updateMethod = $reflection->getMethod('updateRealEstateAddress');
        
        $this->assertTrue($createMethod->isPrivate());
        $this->assertTrue($updateMethod->isPrivate());
    }
}
