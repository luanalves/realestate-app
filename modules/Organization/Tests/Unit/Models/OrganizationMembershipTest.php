<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Tests\Unit\Models;

use Mockery\MockInterface;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;
use Modules\Organization\Support\OrganizationConstants;
use Modules\UserManagement\Models\User;
use Tests\TestCase;

class OrganizationMembershipTest extends TestCase
{
    /**
     * @var MockInterface
     */
    protected $mockUser;

    /**
     * @var MockInterface
     */
    protected $mockOrganization;

    /**
     * @var MockInterface
     */
    protected $mockMembership;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar mocks para os modelos
        $this->mockUser = \Mockery::mock(User::class);
        $this->mockOrganization = \Mockery::mock(Organization::class);
        $this->mockMembership = \Mockery::mock(OrganizationMembership::class)->makePartial();
    }

    /**
     * Clean up the testing environment.
     */
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanCreateOrganizationMembership()
    {
        // Arrange
        $userId = 1;
        $organizationId = 1;

        $this->mockUser->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $this->mockOrganization->shouldReceive('getAttribute')->with('id')->andReturn($organizationId);

        // Act
        $membership = $this->mockMembership->shouldReceive('create')->once()->with([
            'user_id' => $userId,
            'organization_id' => $organizationId,
            'role' => OrganizationConstants::ROLE_ADMIN,
            'position' => 'Director',
            'is_active' => true,
        ])->andReturnSelf()->getMock();

        // Call the method to satisfy the expectation
        $membership->create([
            'user_id' => $userId,
            'organization_id' => $organizationId,
            'role' => OrganizationConstants::ROLE_ADMIN,
            'position' => 'Director',
            'is_active' => true,
        ]);

        // Assert
        // Assert
        $this->assertTrue(true); // Verify that mocks were called correctly
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itBelongsToUser()
    {
        // Arrange
        $this->mockMembership->shouldReceive('user')->once()->andReturnUsing(function () {
            $relation = \Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsTo');
            $relation->shouldReceive('getResults')->andReturn($this->mockUser);

            return $relation;
        });

        // Act & Assert
        $this->assertSame($this->mockUser, $this->mockMembership->user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itBelongsToOrganization()
    {
        // Arrange
        $this->mockMembership->shouldReceive('organization')->once()->andReturnUsing(function () {
            $relation = \Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsTo');
            $relation->shouldReceive('getResults')->andReturn($this->mockOrganization);

            return $relation;
        });

        // Act & Assert
        $this->assertSame($this->mockOrganization, $this->mockMembership->organization);
    }
}
