<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit\GraphQL;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\RealEstate\GraphQL\Mutations\DeleteRealEstateResolver;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Services\RealEstateService;
use Mockery;
use Tests\TestCase;

class DeleteRealEstateResolverTest extends TestCase
{
    /**
     * Test DeleteRealEstateResolver can be instantiated.
     */
    public function testDeleteRealEstateResolverCanBeInstantiated(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $resolver = new DeleteRealEstateResolver($mockService);
        
        $this->assertInstanceOf(DeleteRealEstateResolver::class, $resolver);
    }

    /**
     * Test DeleteRealEstateResolver calls service method correctly.
     */
    public function testDeleteRealEstateResolverCallsServiceMethod(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $mockService->shouldReceive('deleteRealEstate')
            ->once()
            ->with(1)
            ->andReturn($mockRealEstate);
        
        $resolver = new DeleteRealEstateResolver($mockService);
        $result = $resolver(null, ['id' => 1]);
        
        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test DeleteRealEstateResolver handles string id conversion.
     */
    public function testDeleteRealEstateResolverHandlesStringIdConversion(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        $mockRealEstate = Mockery::mock(RealEstate::class);
        
        $mockService->shouldReceive('deleteRealEstate')
            ->once()
            ->with(42)
            ->andReturn($mockRealEstate);
        
        $resolver = new DeleteRealEstateResolver($mockService);
        $result = $resolver(null, ['id' => '42']);
        
        $this->assertSame($mockRealEstate, $result);
    }

    /**
     * Test DeleteRealEstateResolver throws exception when service throws exception.
     */
    public function testDeleteRealEstateResolverThrowsExceptionWhenServiceThrowsException(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        
        $mockService->shouldReceive('deleteRealEstate')
            ->once()
            ->with(999)
            ->andThrow(new ModelNotFoundException('Real Estate not found'));
        
        $resolver = new DeleteRealEstateResolver($mockService);
        
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Real Estate not found');
        
        $resolver(null, ['id' => 999]);
    }

    /**
     * Test DeleteRealEstateResolver handles general exceptions.
     */
    public function testDeleteRealEstateResolverHandlesGeneralExceptions(): void
    {
        $mockService = Mockery::mock(RealEstateService::class);
        
        $mockService->shouldReceive('deleteRealEstate')
            ->once()
            ->with(1)
            ->andThrow(new \Exception('Service error'));
        
        $resolver = new DeleteRealEstateResolver($mockService);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Service error');
        
        $resolver(null, ['id' => 1]);
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
