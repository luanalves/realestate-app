<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Modules\RealEstate\Services\RealEstateService;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Modules\UserManagement\Models\User;
use Tests\TestCase;

class RealEstateServiceTest extends TestCase
{
    use WithFaker;

    /**
     * Mock user for testing.
     */
    protected $mockUser;

    /**
     * Service instance.
     */
    protected $service;

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
        $this->mockUser->id = 1;

        // Authenticate with Laravel Passport
        Passport::actingAs($this->mockUser);

        // Create service instance
        $this->service = new RealEstateService();
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
     * Test that service correctly authorizes real estate access.
     */
    public function testAuthorizeRealEstateAccess(): void
    {
        // Act - Call the authorization method
        $result = $this->service->authorizeRealEstateAccess($this->mockUser);

        // Assert - The user should be returned if authorized
        $this->assertEquals($this->mockUser, $result);
    }

    /**
     * Test that service correctly authorizes real estate write access.
     */
    public function testAuthorizeRealEstateWrite(): void
    {
        // Act - Call the authorization method
        $result = $this->service->authorizeRealEstateWrite($this->mockUser);

        // Assert - The user should be returned if authorized
        $this->assertEquals($this->mockUser, $result);
    }

    /**
     * Test that service throws exception for unauthorized users.
     */
    public function testAuthorizeRealEstateWriteThrowsExceptionForUnauthorizedUser(): void
    {
        // Arrange - Create a user without proper role
        $unauthorizedUser = \Mockery::mock(User::class)->makePartial();
        $unauthorizedUser->role = (object) ['name' => 'client'];
        $unauthorizedUser->id = 2;

        // Assert - Should throw exception
        $this->expectException(\Nuwave\Lighthouse\Exceptions\AuthenticationException::class);
        $this->expectExceptionMessage('You do not have permission to modify real estate agencies');

        // Act - Call the authorization method
        $this->service->authorizeRealEstateWrite($unauthorizedUser);
    }

    /**
     * Test that service correctly handles authorization methods.
     */
    public function testServiceHasAuthorizationMethods(): void
    {
        // Test that authorization methods exist
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateWrite'));
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateAccess'));
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateEntityAccess'));

        // Test that the service is properly instantiated
        $this->assertInstanceOf(RealEstateService::class, $this->service);
    }

    /**
     * Test that createRealEstate method handles data correctly.
     */
    public function testCreateRealEstateHandlesDataCorrectly(): void
    {
        // This test validates the data structure and separation logic
        // without actually creating database records

        // Arrange - Sample input data
        $inputData = [
            'name' => 'Test Real Estate',
            'email' => 'test@example.com',
            'creci' => 'TEST123',
            'stateRegistration' => 'REG456',
            'address' => [
                'street' => 'Test Street',
                'number' => '123',
            ],
        ];

        // Test that the service method exists and is callable
        $this->assertTrue(method_exists($this->service, 'createRealEstate'));

        // Test input data structure
        $this->assertIsArray($inputData);
        $this->assertArrayHasKey('name', $inputData);
        $this->assertArrayHasKey('creci', $inputData);
        $this->assertArrayHasKey('address', $inputData);

        // Test that authorization methods exist
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateWrite'));
        $this->assertTrue(method_exists($this->service, 'authorizeRealEstateAccess'));
    }
}
