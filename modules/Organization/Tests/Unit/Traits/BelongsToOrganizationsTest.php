<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Tests\Unit\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery\MockInterface;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;
use Modules\Organization\Support\OrganizationConstants;
use Tests\TestCase;

class BelongsToOrganizationsTest extends TestCase
{
    /**
     * @var MockInterface
     */
    protected $mockUser;

    /**
     * @var MockInterface
     */
    protected $mockOrganization1;

    /**
     * @var MockInterface
     */
    protected $mockOrganization2;

    /**
     * @var MockInterface
     */
    protected $mockMembership1;

    /**
     * @var MockInterface
     */
    protected $mockMembership2;

    public function setUp(): void
    {
        parent::setUp();

        // Criar mocks
        $this->mockUser = \Mockery::mock(User::class)->makePartial();
        $this->mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $this->mockOrganization1 = \Mockery::mock(Organization::class)->makePartial();
        $this->mockOrganization1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $this->mockOrganization1->shouldReceive('getAttribute')->with('name')->andReturn('Test Organization 1');

        $this->mockOrganization2 = \Mockery::mock(Organization::class)->makePartial();
        $this->mockOrganization2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $this->mockOrganization2->shouldReceive('getAttribute')->with('name')->andReturn('Test Organization 2');

        $this->mockMembership1 = \Mockery::mock(OrganizationMembership::class)->makePartial();
        $this->mockMembership1->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $this->mockMembership1->shouldReceive('getAttribute')->with('organization_id')->andReturn(1);
        $this->mockMembership1->shouldReceive('getAttribute')->with('role')->andReturn(OrganizationConstants::ROLE_ADMIN);
        $this->mockMembership1->shouldReceive('getAttribute')->with('is_active')->andReturn(true);
        $this->mockMembership1->shouldReceive('organization')->andReturn($this->mockOrganization1);

        $this->mockMembership2 = \Mockery::mock(OrganizationMembership::class)->makePartial();
        $this->mockMembership2->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $this->mockMembership2->shouldReceive('getAttribute')->with('organization_id')->andReturn(2);
        $this->mockMembership2->shouldReceive('getAttribute')->with('role')->andReturn(OrganizationConstants::ROLE_MEMBER);
        $this->mockMembership2->shouldReceive('getAttribute')->with('is_active')->andReturn(false);
        $this->mockMembership2->shouldReceive('organization')->andReturn($this->mockOrganization2);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanGetOrganizationMemberships()
    {
        // Arrange
        $memberships = new Collection([$this->mockMembership1, $this->mockMembership2]);
        $membershipRelation = \Mockery::mock(HasMany::class);
        $membershipRelation->shouldReceive('get')->andReturn($memberships);

        $this->mockUser->shouldReceive('organizationMemberships')->andReturn($membershipRelation);
        $this->mockUser->shouldReceive('getAttribute')->with('organizationMemberships')->andReturn($memberships);

        // Act & Assert
        $result = $this->mockUser->organizationMemberships;
        $this->assertCount(2, $result);
        $this->assertSame($this->mockMembership1, $result[0]);
        $this->assertSame($this->mockMembership2, $result[1]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanGetActiveOrganizationMemberships()
    {
        // Arrange
        $activeMemberships = new Collection([$this->mockMembership1]);
        $membershipRelation = \Mockery::mock(HasMany::class);
        $membershipRelation->shouldReceive('where')->with('is_active', true)->andReturnSelf();
        $membershipRelation->shouldReceive('get')->andReturn($activeMemberships);

        $this->mockUser->shouldReceive('organizationMemberships')->andReturn($membershipRelation);
        $this->mockUser->shouldReceive('getAttribute')->with('activeOrganizationMemberships')->andReturn($activeMemberships);

        // Act & Assert
        $result = $this->mockUser->activeOrganizationMemberships;
        $this->assertCount(1, $result);
        $this->assertSame($this->mockMembership1, $result[0]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanGetOrganizationMembershipsWithRole()
    {
        // Arrange
        $adminMemberships = new Collection([$this->mockMembership1]);
        $memberMemberships = new Collection([$this->mockMembership2]);

        $adminRelation = \Mockery::mock(HasMany::class);
        $adminRelation->shouldReceive('where')->with('role', OrganizationConstants::ROLE_ADMIN)->andReturnSelf();
        $adminRelation->shouldReceive('get')->andReturn($adminMemberships);
        $adminRelation->shouldReceive('first')->andReturn($this->mockMembership1);

        $memberRelation = \Mockery::mock(HasMany::class);
        $memberRelation->shouldReceive('where')->with('role', OrganizationConstants::ROLE_MEMBER)->andReturnSelf();
        $memberRelation->shouldReceive('get')->andReturn($memberMemberships);
        $memberRelation->shouldReceive('first')->andReturn($this->mockMembership2);

        $this->mockUser->shouldReceive('organizationMembershipsWithRole')
            ->with(OrganizationConstants::ROLE_ADMIN)
            ->andReturn($adminRelation);

        $this->mockUser->shouldReceive('organizationMembershipsWithRole')
            ->with(OrganizationConstants::ROLE_MEMBER)
            ->andReturn($memberRelation);

        // Act & Assert
        $admins = $this->mockUser->organizationMembershipsWithRole(OrganizationConstants::ROLE_ADMIN);
        $members = $this->mockUser->organizationMembershipsWithRole(OrganizationConstants::ROLE_MEMBER);

        $this->assertSame($adminRelation, $admins);
        $this->assertSame($memberRelation, $members);
        $this->assertCount(1, $admins->get());
        $this->assertCount(1, $members->get());
    }
}
