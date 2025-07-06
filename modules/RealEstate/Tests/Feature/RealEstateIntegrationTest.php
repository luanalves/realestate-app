<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Modules\Organization\Models\Organization;
use Modules\RealEstate\Models\RealEstate;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Modules\UserManagement\Models\User;
use Tests\TestCase;

class RealEstateIntegrationTest extends TestCase
{
    use WithFaker;

    /**
     * Mock user for testing.
     */
    protected $mockUser;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock user for authentication
        $this->mockUser = \Mockery::mock(User::class)->makePartial();
        $this->mockUser->shouldReceive('getAuthIdentifier')->andReturn(1);
        $this->mockUser->shouldReceive('withAccessToken')->andReturnSelf();
        $this->mockUser->role = (object) ['name' => RolesSeeder::ROLE_SUPER_ADMIN];

        // Authenticate with Laravel Passport
        Passport::actingAs($this->mockUser);
    }

    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that RealEstate model correctly delegates to Organization attributes.
     */
    public function testRealEstateModelDelegatesOrganizationAttributes(): void
    {
        // Arrange - Create mock organization and real estate
        $mockOrganization = \Mockery::mock(Organization::class)->makePartial();
        $mockOrganization->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn('Test Real Estate Company');
        $mockOrganization->shouldReceive('hasAttribute')
            ->with('name')
            ->andReturn(true);

        $mockRealEstate = \Mockery::mock(RealEstate::class)->makePartial();
        $mockRealEstate->shouldReceive('relationLoaded')
            ->with('organization')
            ->andReturn(true);
        $mockRealEstate->organization = $mockOrganization;
        $mockRealEstate->shouldReceive('hasGetMutator')
            ->andReturn(false);

        // Act - Try to access organization attribute through real estate
        $result = $mockRealEstate->name;

        // Assert - The organization name should be accessible through the real estate
        $this->assertEquals('Test Real Estate Company', $result);
    }

    /**
     * Test that RealEstate model has correct relationship with Organization.
     */
    public function testRealEstateHasOrganizationRelationship(): void
    {
        // Arrange - Create a real estate instance
        $realEstate = new RealEstate();

        // Act - Check if the relationship exists
        $relationship = $realEstate->organization();

        // Assert - The relationship should be correctly defined
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relationship);
        $this->assertEquals('organization_id', $relationship->getForeignKeyName());
        $this->assertEquals('id', $relationship->getOwnerKeyName());
    }

    /**
     * Test that RealEstate constants are correctly defined.
     */
    public function testRealEstateConstants(): void
    {
        // Act - Get the organization type
        $organizationType = RealEstate::getOrganizationType();

        // Assert - The organization type should be defined
        $this->assertNotEmpty($organizationType);
        $this->assertIsString($organizationType);
    }

    /**
     * Test that RealEstate scope methods work correctly.
     */
    public function testRealEstateScopeWhereCRECI(): void
    {
        // Arrange - Create a real estate query builder mock
        $mockBuilder = \Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockBuilder->shouldReceive('where')
            ->once()
            ->with('creci', 'TEST123')
            ->andReturn($mockBuilder);

        // Act - Apply the scope
        $realEstate = new RealEstate();
        $result = $realEstate->scopeWhereCRECI($mockBuilder, 'TEST123');

        // Assert - The scope should apply the correct where clause
        $this->assertEquals($mockBuilder, $result);
    }
}
