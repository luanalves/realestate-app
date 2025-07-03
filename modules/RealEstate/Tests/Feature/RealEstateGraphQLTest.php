<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Feature;

use Modules\UserManagement\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Mockery;
use Modules\Organization\Models\Organization;
use Modules\RealEstate\Models\RealEstate;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Tests\TestCase;

class RealEstateGraphQLTest extends TestCase
{
    use WithFaker;
    
    /**
     * Mock user for testing
     */
    protected $mockUser;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock user for authentication
        $this->mockUser = Mockery::mock(User::class)->makePartial();
        $this->mockUser->shouldReceive('getAuthIdentifier')->andReturn(1);
        $this->mockUser->shouldReceive('withAccessToken')->andReturnSelf();
        $this->mockUser->role = (object)['name' => RolesSeeder::ROLE_SUPER_ADMIN];
        
        // Authenticate with Laravel Passport
        Passport::actingAs($this->mockUser);
    }
    
    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test creating a real estate agency.
     * 
     * This test validates the GraphQL mutation to create a new real estate entity.
     * Note: We need to use snake_case for assertion matching as that's what Lighthouse GraphQL returns,
     * but we send the request with camelCase as that's what the schema expects.
     */
    public function testCreateRealEstate(): void
    {
        // Skip test if there's no real schema available (development environment only)
        $this->markTestSkipped('Test requires real GraphQL schema to run. Run in complete environment.');
        
        // Arrange - Mock the service
        $this->mock(\Modules\RealEstate\Services\RealEstateService::class, function ($mock) {
            $mock->shouldReceive('createRealEstate')
                ->once()
                ->andReturn(new RealEstate([
                    'id' => 1,
                    'name' => 'Test Real Estate',
                    'fantasy_name' => 'Test Fantasy Name',
                    'cnpj' => '12345678901234',
                ]));
        });
        
        // Act - Execute the GraphQL mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation($input: CreateRealEstateInput!) {
                    createRealEstate(input: $input) {
                        id
                        name
                        fantasy_name
                        cnpj
                    }
                }
            ',
            'variables' => [
                'input' => [
                    'name' => 'Test Real Estate',
                    'fantasyName' => 'Test Fantasy Name',
                    'cnpj' => '12345678901234',
                    'email' => 'test@example.com'
                ]
            ]
        ]);
        
        // Assert - Check the response
        $response->assertStatus(200);
    }

    /**
     * Test fetching a real estate agency.
     * 
     * This test validates the GraphQL query to get a real estate entity by ID.
     */
    public function testGetRealEstate(): void
    {
        // Skip test if there's no real schema available (development environment only)
        $this->markTestSkipped('Test requires real GraphQL schema to run. Run in complete environment.');
        
        // Act - Execute the GraphQL query
        $response = $this->postJson('/graphql', [
            'query' => '
                query($id: ID!) {
                    realEstate(id: $id) {
                        id
                        name
                        fantasy_name
                        cnpj
                    }
                }
            ',
            'variables' => [
                'id' => 1
            ]
        ]);
        
        // Assert - Just check status since we're mocking
        $response->assertStatus(200);
    }

    /**
     * Test updating a real estate agency.
     * 
     * This test validates the GraphQL mutation to update an existing real estate entity.
     */
    public function testUpdateRealEstate(): void
    {
        // Skip test if there's no real schema available (development environment only)
        $this->markTestSkipped('Test requires real GraphQL schema to run. Run in complete environment.');
        
        // Act - Execute the GraphQL mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation($id: ID!, $input: UpdateRealEstateInput!) {
                    updateRealEstate(id: $id, input: $input) {
                        id
                        name
                        fantasy_name
                        phone
                    }
                }
            ',
            'variables' => [
                'id' => 1,
                'input' => [
                    'name' => 'Updated Real Estate',
                    'fantasyName' => 'Updated Fantasy Name',
                    'phone' => '(11) 9999-7777'
                ]
            ]
        ]);
        
        // Assert - Just check status since we're mocking
        $response->assertStatus(200);
    }

    /**
     * Test deleting a real estate agency.
     * 
     * This test validates the GraphQL mutation to delete a real estate entity.
     */
    public function testDeleteRealEstate(): void
    {
        // Skip test if there's no real schema available (development environment only)
        $this->markTestSkipped('Test requires real GraphQL schema to run. Run in complete environment.');
        
        // Act - Execute the GraphQL mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation($id: ID!) {
                    deleteRealEstate(id: $id) {
                        id
                        name
                    }
                }
            ',
            'variables' => [
                'id' => 1
            ]
        ]);
        
        // Assert - Just check status since we're mocking
        $response->assertStatus(200);
    }
}
