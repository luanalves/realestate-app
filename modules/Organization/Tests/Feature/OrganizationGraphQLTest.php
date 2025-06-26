<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;

class OrganizationGraphQLTest extends TestCase
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
     * Test creating an organization via GraphQL mutation.
     */
    public function testCreateOrganization(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation createOrganization($input: CreateOrganizationInput!) {
                    createOrganization(input: $input) {
                        id
                        name
                        fantasy_name
                        cnpj
                        email
                        phone
                        website
                    }
                }
            ',
            'variables' => [
                'input' => [
                    'name' => 'Test Organization',
                    'fantasy_name' => 'Test Org',
                    'cnpj' => '12345678901234',
                    'description' => 'This is a test organization',
                    'email' => 'test@organization.com',
                    'phone' => '1122223333',
                    'website' => 'https://testorg.com',
                    'active' => true
                ]
            ]
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['createOrganization' => [
                'id', 'name', 'fantasy_name', 'cnpj', 'email', 'phone', 'website'
            ]]])
            ->assertJsonPath('data.createOrganization.name', 'Test Organization')
            ->assertJsonPath('data.createOrganization.fantasy_name', 'Test Org')
            ->assertJsonPath('data.createOrganization.cnpj', '12345678901234')
            ->assertJsonPath('data.createOrganization.email', 'test@organization.com');
    }

    /**
     * Test creating an organization with an address.
     */
    public function testCreateOrganizationWithAddress(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation createOrganizationWithAddress($input: CreateOrganizationInput!) {
                    createOrganization(input: $input) {
                        id
                        name
                        fantasy_name
                        cnpj
                        email
                        addresses {
                            id
                            type
                            street
                            number
                            city
                            state
                            zip_code
                            country
                        }
                    }
                }
            ',
            'variables' => [
                'input' => [
                    'name' => 'Organization With Address',
                    'fantasy_name' => 'Address Org',
                    'cnpj' => '43210987654321',
                    'email' => 'address@organization.com',
                    'active' => true,
                    'address' => [
                        'type' => 'headquarters',
                        'street' => 'Main Street',
                        'number' => '123',
                        'neighborhood' => 'Downtown',
                        'city' => 'São Paulo',
                        'state' => 'SP',
                        'zipCode' => '01234567',
                        'country' => 'BR'
                    ]
                ]
            ]
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['createOrganization' => [
                'id', 'name', 'fantasy_name', 'cnpj', 'email',
                'addresses' => [
                    ['id', 'type', 'street', 'number', 'city', 'state', 'zip_code', 'country']
                ]
            ]]])
            ->assertJsonPath('data.createOrganization.name', 'Organization With Address')
            ->assertJsonPath('data.createOrganization.cnpj', '43210987654321');
    }

    /**
     * Test updating an organization via GraphQL mutation.
     */
    public function testUpdateOrganization(): void
    {
        // First create an organization to update
        $createResponse = $this->postJson('/graphql', [
            'query' => '
                mutation CreateOrg($input: CreateOrganizationInput!) {
                    createOrganization(input: $input) {
                        id
                        name
                        fantasy_name
                        description
                    }
                }
            ',
            'variables' => [
                'input' => [
                    'name' => 'Organization To Update',
                    'fantasy_name' => 'Update Me',
                    'cnpj' => '98765432109876',
                    'email' => 'update@test.com',
                    'description' => 'This will be updated',
                    'active' => true
                ]
            ]
        ]);
        
        $createResponse->assertStatus(200);
        $organizationId = $createResponse->json('data.createOrganization.id');
        
        // Now update the organization
        $updateResponse = $this->postJson('/graphql', [
            'query' => '
                mutation UpdateOrg($id: ID!, $input: UpdateOrganizationInput!) {
                    updateOrganization(id: $id, input: $input) {
                        id
                        name
                        fantasy_name
                        description
                        email
                        phone
                        website
                        active
                    }
                }
            ',
            'variables' => [
                'id' => $organizationId,
                'input' => [
                    'name' => 'Updated Organization Name',
                    'description' => 'This organization has been updated',
                    'phone' => '9876543210',
                    'website' => 'https://updated.example.com'
                ]
            ]
        ]);
        
        $updateResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['updateOrganization' => [
                'id', 'name', 'fantasy_name', 'description', 'email', 'phone', 'website', 'active'
            ]]])
            ->assertJsonPath('data.updateOrganization.id', $organizationId)
            ->assertJsonPath('data.updateOrganization.name', 'Updated Organization Name')
            ->assertJsonPath('data.updateOrganization.description', 'This organization has been updated')
            ->assertJsonPath('data.updateOrganization.phone', '9876543210')
            ->assertJsonPath('data.updateOrganization.website', 'https://updated.example.com')
            ->assertJsonPath('data.updateOrganization.fantasy_name', 'Update Me');
    }

    /**
     * Test deleting an organization via GraphQL mutation.
     */
    public function testDeleteOrganization(): void
    {
        // First create an organization to delete
        $createResponse = $this->postJson('/graphql', [
            'query' => '
                mutation CreateOrg($input: CreateOrganizationInput!) {
                    createOrganization(input: $input) {
                        id
                        name
                    }
                }
            ',
            'variables' => [
                'input' => [
                    'name' => 'Organization To Delete',
                    'cnpj' => '11122233344455',
                    'email' => 'delete@test.com',
                    'active' => true
                ]
            ]
        ]);
        
        $createResponse->assertStatus(200);
        $organizationId = $createResponse->json('data.createOrganization.id');
        
        // Now delete the organization
        $deleteResponse = $this->postJson('/graphql', [
            'query' => '
                mutation DeleteOrg($id: ID!) {
                    deleteOrganization(id: $id) {
                        id
                        name
                    }
                }
            ',
            'variables' => [
                'id' => $organizationId
            ]
        ]);
        
        $deleteResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['deleteOrganization' => [
                'id', 'name'
            ]]])
            ->assertJsonPath('data.deleteOrganization.id', $organizationId)
            ->assertJsonPath('data.deleteOrganization.name', 'Organization To Delete');
            
        // Try to fetch the deleted organization - it should fail or return null
        $fetchResponse = $this->postJson('/graphql', [
            'query' => '
                query GetDeletedOrg($id: ID!) {
                    organization(id: $id) {
                        id
                        name
                    }
                }
            ',
            'variables' => [
                'id' => $organizationId
            ]
        ]);
        
        // Organization should be null or response should indicate it doesn't exist
        $fetchResponse->assertStatus(200);
        $this->assertNull($fetchResponse->json('data.organization'));
    }
}
