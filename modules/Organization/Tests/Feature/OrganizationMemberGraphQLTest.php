<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Tests\Feature;

use Modules\UserManagement\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Mockery;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;
use Modules\Organization\Support\OrganizationConstants;
use Tests\TestCase;

class OrganizationMemberGraphQLTest extends TestCase
{
    use WithFaker;
    
    /**
     * Mock user for testing
     */
    protected $mockUser;
    protected $organization;
    protected $regularUser;

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
        
        // Create a test organization
        $this->organization = Organization::create([
            'name' => 'Test Organization',
            'fantasy_name' => 'Test Org',
            'email' => 'test@organization.com',
            'active' => true,
        ]);
        
        // Create a regular user to be added as a member
        $this->regularUser = User::factory()->create();
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
     * Test adding a member to an organization via GraphQL mutation.
     */
    public function testAddOrganizationMember(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation AddMember($organizationId: ID!, $userId: ID!, $role: String!, $position: String) {
                    addOrganizationMember(
                        organizationId: $organizationId
                        userId: $userId
                        role: $role
                        position: $position
                    )
                }
            ',
            'variables' => [
                'organizationId' => (string)$this->organization->id,
                'userId' => (string)$this->regularUser->id,
                'role' => OrganizationConstants::ROLE_MEMBER,
                'position' => 'Staff'
            ]
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'addOrganizationMember' => true
                ]
            ]);
        
        // Verify the membership was created in the database
        $this->assertDatabaseHas('organization_members', [
            'user_id' => $this->regularUser->id,
            'organization_id' => $this->organization->id,
            'role' => OrganizationConstants::ROLE_MEMBER,
            'position' => 'Staff',
            'is_active' => true,
        ]);
    }

    /**
     * Test updating a member in an organization via GraphQL mutation.
     */
    public function testUpdateOrganizationMember(): void
    {
        // First add a member
        OrganizationMembership::create([
            'user_id' => $this->regularUser->id,
            'organization_id' => $this->organization->id,
            'role' => OrganizationConstants::ROLE_MEMBER,
            'position' => 'Staff',
            'is_active' => true,
        ]);
        
        // Now update the member
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation UpdateMember($organizationId: ID!, $userId: ID!, $role: String, $position: String, $is_active: Boolean) {
                    updateOrganizationMember(
                        organizationId: $organizationId
                        userId: $userId
                        role: $role
                        position: $position
                        is_active: $is_active
                    )
                }
            ',
            'variables' => [
                'organizationId' => (string)$this->organization->id,
                'userId' => (string)$this->regularUser->id,
                'role' => OrganizationConstants::ROLE_ADMIN,
                'position' => 'Manager',
                'is_active' => true
            ]
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'updateOrganizationMember' => true
                ]
            ]);
        
        // Verify the membership was updated in the database
        $this->assertDatabaseHas('organization_members', [
            'user_id' => $this->regularUser->id,
            'organization_id' => $this->organization->id,
            'role' => OrganizationConstants::ROLE_ADMIN,
            'position' => 'Manager',
            'is_active' => true,
        ]);
    }

    /**
     * Test removing a member from an organization via GraphQL mutation.
     */
    public function testRemoveOrganizationMember(): void
    {
        // First add a member
        OrganizationMembership::create([
            'user_id' => $this->regularUser->id,
            'organization_id' => $this->organization->id,
            'role' => OrganizationConstants::ROLE_MEMBER,
            'position' => 'Staff',
            'is_active' => true,
        ]);
        
        // Now remove the member
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation RemoveMember($organizationId: ID!, $userId: ID!) {
                    removeOrganizationMember(
                        organizationId: $organizationId
                        userId: $userId
                    )
                }
            ',
            'variables' => [
                'organizationId' => (string)$this->organization->id,
                'userId' => (string)$this->regularUser->id
            ]
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'removeOrganizationMember' => true
                ]
            ]);
        
        // Verify the membership was removed from the database
        $this->assertDatabaseMissing('organization_members', [
            'user_id' => $this->regularUser->id,
            'organization_id' => $this->organization->id,
        ]);
    }
}
