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
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;
use Tests\TestCase;

class MembersByOrganizationIdTest extends TestCase
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

        // Create mock user for authentication
        $this->mockUser = \Mockery::mock(User::class)->makePartial();
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
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * Test getting members by organization ID.
     */
    public function testGetMembersByOrganizationId(): void
    {
        // Mock organization
        $mockOrganization = \Mockery::mock(Organization::class)->makePartial();

        // Create mock memberships
        $mockMemberships = collect([
            $this->createMockMembership(1, 1, 'admin'),
            $this->createMockMembership(2, 2, 'member'),
            $this->createMockMembership(3, 3, 'manager'),
        ]);

        // Mock the memberships method
        $membersQuery = \Mockery::mock('\Illuminate\Database\Eloquent\Relations\HasMany');
        $membersQuery->shouldReceive('with')->with('user')->andReturnSelf();
        $membersQuery->shouldReceive('when')->andReturnSelf();
        $membersQuery->shouldReceive('get')->andReturn($mockMemberships);

        // Set up organization mocking
        $mockOrganization->shouldReceive('memberships')->andReturn($membersQuery);

        // Mock Organization::find
        $this->instance(
            Organization::class,
            \Mockery::mock(Organization::class, function ($mock) use ($mockOrganization) {
                $mock->shouldReceive('find')->with('1')->andReturn($mockOrganization);
            })
        );

        $response = $this->postJson('/graphql', [
            'query' => /* @lang GraphQL */ '
                query {
                    membersByOrganizationId(organizationId: "1") {
                        id
                        role
                    }
                }
            ',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'membersByOrganizationId' => [
                        ['id' => '1', 'role' => 'admin'],
                        ['id' => '2', 'role' => 'member'],
                        ['id' => '3', 'role' => 'manager'],
                    ],
                ],
            ]);
    }

    /**
     * Test filtering members by role.
     */
    public function testGetMembersByRole(): void
    {
        // Mock organization
        $mockOrganization = \Mockery::mock(Organization::class)->makePartial();

        // Create mock memberships filtered by role
        $mockMemberships = collect([
            $this->createMockMembership(1, 1, 'admin'),
        ]);

        // Mock the members method with where clause
        $membersQuery = \Mockery::mock('\Illuminate\Database\Eloquent\Relations\HasMany');
        $membersQuery->shouldReceive('with')->with('user')->andReturnSelf();

        // Handle the when clause for filtering by role
        $membersQuery->shouldReceive('when')
            ->withArgs(function ($condition, $callback) {
                if ($condition === true) {
                    $query = \Mockery::mock('\Illuminate\Database\Eloquent\Builder');
                    $query->shouldReceive('where')->with('role', 'admin')->andReturnSelf();
                    $callback($query);
                }

                return true;
            })
            ->andReturnSelf();

        // Handle the when clause for filtering by active status
        $membersQuery->shouldReceive('when')
            ->withArgs(function ($condition, $callback) {
                // This condition should be false since we're not filtering by active
                return $condition === false;
            })
            ->andReturnSelf();

        $membersQuery->shouldReceive('get')->andReturn($mockMemberships);

        // Set up organization mocking
        $mockOrganization->shouldReceive('memberships')->andReturn($membersQuery);

        // Mock Organization::find
        $this->instance(
            Organization::class,
            \Mockery::mock(Organization::class, function ($mock) use ($mockOrganization) {
                $mock->shouldReceive('find')->with('1')->andReturn($mockOrganization);
            })
        );

        $response = $this->postJson('/graphql', [
            'query' => /* @lang GraphQL */ '
                query {
                    membersByOrganizationId(organizationId: "1", role: "admin") {
                        id
                        role
                    }
                }
            ',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'membersByOrganizationId' => [
                        ['id' => '1', 'role' => 'admin'],
                    ],
                ],
            ]);
    }

    /**
     * Create a mock membership object.
     */
    private function createMockMembership($id, $userId, $role): OrganizationMembership
    {
        $mockUser = \Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $mockUser->shouldReceive('getAttribute')->with('name')->andReturn("User {$userId}");
        $mockUser->shouldReceive('getAttribute')->with('email')->andReturn("user{$userId}@example.com");

        $mockMembership = \Mockery::mock(OrganizationMembership::class)->makePartial();
        $mockMembership->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $mockMembership->shouldReceive('getAttribute')->with('role')->andReturn($role);
        $mockMembership->shouldReceive('getAttribute')->with('user')->andReturn($mockUser);
        $mockMembership->shouldReceive('getAttribute')->with('organization_id')->andReturn(1);
        $mockMembership->shouldReceive('getAttribute')->with('position')->andReturn("Position {$id}");
        $mockMembership->shouldReceive('getAttribute')->with('is_active')->andReturn(true);

        return $mockMembership;
    }
}
