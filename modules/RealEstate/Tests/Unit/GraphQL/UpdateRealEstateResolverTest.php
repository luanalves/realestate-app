<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit\GraphQL;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\RealEstate\GraphQL\Mutations\UpdateRealEstateResolver;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Mockery;
use Tests\TestCase;

class UpdateRealEstateResolverTest extends TestCase
{
    /**
     * Test UpdateRealEstateResolver can be instantiated.
     */
    public function testUpdateRealEstateResolverCanBeInstantiated(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $resolver = new UpdateRealEstateResolver($mockService);
        
        $this->assertInstanceOf(UpdateRealEstateResolver::class, $resolver);
    }

    /**
     * Test UpdateRealEstateResolver calls service method correctly.
     */
    public function testUpdateRealEstateResolverCallsServiceMethod(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $updateData = [
            'name' => 'Updated Real Estate',
            'description' => 'Updated description'
        ];
        
        $mockService->shouldReceive('updateRealEstate')
            ->once()
            ->with(1, $updateData)
            ->andReturn($mockRealEstate);
        
        $resolver = new UpdateRealEstateResolver($mockService);
        $result = $resolver(null, array_merge(['id' => 1], $updateData));
        
        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test UpdateRealEstateResolver handles string id conversion.
     */
    public function testUpdateRealEstateResolverHandlesStringIdConversion(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $updateData = [
            'name' => 'Updated Real Estate',
            'description' => 'Updated description'
        ];
        
        $mockService->shouldReceive('updateRealEstate')
            ->once()
            ->with(42, $updateData)
            ->andReturn($mockRealEstate);
        
        $resolver = new UpdateRealEstateResolver($mockService);
        $result = $resolver(null, array_merge(['id' => '42'], $updateData));
        
        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test UpdateRealEstateResolver handles complex input data.
     */
    public function testUpdateRealEstateResolverHandlesComplexInputData(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $updateData = [
            'name' => 'Complex Updated Real Estate',
            'creci' => '67890',
            'description' => 'Complex updated description',
            'email' => 'updated@example.com',
            'phone' => '0987654321'
        ];
        
        $mockService->shouldReceive('updateRealEstate')
            ->once()
            ->with(1, $updateData)
            ->andReturn($mockRealEstate);
        
        $resolver = new UpdateRealEstateResolver($mockService);
        $result = $resolver(null, array_merge(['id' => 1], $updateData));
        
        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test UpdateRealEstateResolver properly removes id from args.
     */
    public function testUpdateRealEstateResolverProperlyRemovesIdFromArgs(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $updateData = [
            'name' => 'Test Real Estate',
            'description' => 'Test description'
        ];
        
        // The service should receive only the update data without the id
        $mockService->shouldReceive('updateRealEstate')
            ->once()
            ->with(1, $updateData)
            ->andReturn($mockRealEstate);
        
        $resolver = new UpdateRealEstateResolver($mockService);
        $result = $resolver(null, array_merge(['id' => 1], $updateData));
        
        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test UpdateRealEstateResolver throws exception when service throws exception.
     */
    public function testUpdateRealEstateResolverThrowsExceptionWhenServiceThrowsException(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        
        $updateData = [
            'name' => 'Test Real Estate',
            'description' => 'Test description'
        ];
        
        $mockService->shouldReceive('updateRealEstate')
            ->once()
            ->with(999, $updateData)
            ->andThrow(new ModelNotFoundException('Real Estate not found'));
        
        $resolver = new UpdateRealEstateResolver($mockService);
        
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Real Estate not found');
        
        $resolver(null, array_merge(['id' => 999], $updateData));
    }

    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
