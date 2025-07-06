<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit\GraphQL;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\RealEstate\GraphQL\Queries\RealEstateResolver;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Mockery;
use Tests\TestCase;

class RealEstateResolverTest extends TestCase
{
    /**
     * Test RealEstateResolver can be instantiated.
     */
    public function testRealEstateResolverCanBeInstantiated(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $resolver = new RealEstateResolver($mockService);
        
        $this->assertInstanceOf(RealEstateResolver::class, $resolver);
    }

    /**
     * Test RealEstateResolver calls service method correctly.
     */
    public function testRealEstateResolverCallsServiceMethod(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $mockService->shouldReceive('getRealEstateById')
            ->once()
            ->with(1)
            ->andReturn($mockRealEstate);
        
        $resolver = new RealEstateResolver($mockService);
        $result = $resolver(null, ['id' => 1]);
        
        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test RealEstateResolver throws exception when service throws exception.
     */
    public function testRealEstateResolverThrowsExceptionWhenServiceThrowsException(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        
        $mockService->shouldReceive('getRealEstateById')
            ->once()
            ->with(999)
            ->andThrow(new ModelNotFoundException('Real Estate not found'));
        
        $resolver = new RealEstateResolver($mockService);
        
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Real Estate not found');
        
        $resolver(null, ['id' => 999]);
    }

    /**
     * Test RealEstateResolver handles string id conversion.
     */
    public function testRealEstateResolverHandlesStringIdConversion(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $mockService->shouldReceive('getRealEstateById')
            ->once()
            ->with(42)
            ->andReturn($mockRealEstate);
        
        $resolver = new RealEstateResolver($mockService);
        $result = $resolver(null, ['id' => '42']);
        
        $this->assertSame($mockRealEstate, $result);
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
