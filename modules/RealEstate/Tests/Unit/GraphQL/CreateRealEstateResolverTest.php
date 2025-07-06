<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit\GraphQL;

use Modules\RealEstate\GraphQL\Mutations\CreateRealEstateResolver;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Tests\TestCase;

class CreateRealEstateResolverTest extends TestCase
{
    /**
     * Test CreateRealEstateResolver can be instantiated.
     */
    public function testCreateRealEstateResolverCanBeInstantiated(): void
    {
        $mockService = \Mockery::mock(RealEstateService::class);
        $resolver = new CreateRealEstateResolver($mockService);

        $this->assertInstanceOf(CreateRealEstateResolver::class, $resolver);
    }

    /**
     * Test CreateRealEstateResolver calls service method correctly.
     */
    public function testCreateRealEstateResolverCallsServiceMethod(): void
    {
        $mockService = \Mockery::mock(RealEstateService::class);
        $mockRealEstate = \Mockery::mock(RealEstate::class);

        $inputData = [
            'name' => 'Test Real Estate',
            'creci' => '12345',
            'description' => 'Test description',
        ];

        $mockService->shouldReceive('createRealEstate')
            ->once()
            ->with($inputData)
            ->andReturn($mockRealEstate);

        $resolver = new CreateRealEstateResolver($mockService);
        $result = $resolver(null, ['input' => $inputData]);

        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test CreateRealEstateResolver handles complex input data.
     */
    public function testCreateRealEstateResolverHandlesComplexInputData(): void
    {
        $mockService = \Mockery::mock(RealEstateService::class);
        $mockRealEstate = \Mockery::mock(RealEstate::class);

        $inputData = [
            'name' => 'Complex Real Estate',
            'creci' => '67890',
            'description' => 'Complex description',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'address' => [
                'street' => 'Test Street',
                'number' => '123',
                'city' => 'Test City',
            ],
        ];

        $mockService->shouldReceive('createRealEstate')
            ->once()
            ->with($inputData)
            ->andReturn($mockRealEstate);

        $resolver = new CreateRealEstateResolver($mockService);
        $result = $resolver(null, ['input' => $inputData]);

        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test CreateRealEstateResolver handles service exceptions.
     */
    public function testCreateRealEstateResolverHandlesServiceExceptions(): void
    {
        $mockService = \Mockery::mock(RealEstateService::class);

        $inputData = [
            'name' => 'Test Real Estate',
            'creci' => '12345',
        ];

        $mockService->shouldReceive('createRealEstate')
            ->once()
            ->with($inputData)
            ->andThrow(new \Exception('Service error'));

        $resolver = new CreateRealEstateResolver($mockService);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Service error');

        $resolver(null, ['input' => $inputData]);
    }

    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
