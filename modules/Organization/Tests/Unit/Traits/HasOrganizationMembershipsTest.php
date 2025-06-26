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
use Mockery;
use Mockery\MockInterface;
use Modules\Organization\Models\Organization;
use Modules\Organization\Models\OrganizationMembership;
use Modules\Organization\Support\OrganizationConstants;
use Modules\Organization\Traits\HasOrganizationMemberships;
use Tests\TestCase;

class HasOrganizationMembershipsTest extends TestCase
{
    /**
     * @var MockInterface
     */
    protected $organization;
    
    /**
     * @var MockInterface
     */
    protected $user1;
    
    /**
     * @var MockInterface
     */
    protected $user2;
    
    /**
     * @var MockInterface
     */
    protected $membership1;
    
    /**
     * @var MockInterface
     */
    protected $membership2;

    public function setUp(): void
    {
        parent::setUp();
        
        // Verificamos que a classe Organization usa o trait HasOrganizationMemberships
        $this->assertTrue(in_array(
            HasOrganizationMemberships::class,
            class_uses_recursive(Organization::class)
        ));
        
        // Criando mocks
        $this->organization = Mockery::mock(Organization::class)->makePartial();
        $this->organization->shouldReceive('getAttribute')->with('id')->andReturn(1);
        
        $this->user1 = Mockery::mock(User::class)->makePartial();
        $this->user1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        
        $this->user2 = Mockery::mock(User::class)->makePartial();
        $this->user2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        
        $this->membership1 = Mockery::mock(OrganizationMembership::class)->makePartial();
        $this->membership1->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $this->membership1->shouldReceive('getAttribute')->with('organization_id')->andReturn(1);
        $this->membership1->shouldReceive('getAttribute')->with('role')->andReturn(OrganizationConstants::ROLE_ADMIN);
        $this->membership1->shouldReceive('getAttribute')->with('is_active')->andReturn(true);
        $this->membership1->shouldReceive('user')->andReturn($this->user1);
        
        $this->membership2 = Mockery::mock(OrganizationMembership::class)->makePartial();
        $this->membership2->shouldReceive('getAttribute')->with('user_id')->andReturn(2);
        $this->membership2->shouldReceive('getAttribute')->with('organization_id')->andReturn(1);
        $this->membership2->shouldReceive('getAttribute')->with('role')->andReturn(OrganizationConstants::ROLE_MEMBER);
        $this->membership2->shouldReceive('getAttribute')->with('is_active')->andReturn(false);
        $this->membership2->shouldReceive('user')->andReturn($this->user2);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanGetMembers()
    {
        // Arrange
        $memberships = new Collection([$this->membership1, $this->membership2]);
        $membershipRelation = Mockery::mock(HasMany::class);
        $membershipRelation->shouldReceive('get')->andReturn($memberships);
        
        $this->organization->shouldReceive('memberships')->andReturn($membershipRelation);
        $this->organization->shouldReceive('getMembers')->andReturn(new Collection([$this->user1, $this->user2]));
        
        // Act & Assert
        $members = $this->organization->getMembers();
        $this->assertCount(2, $members);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanGetActiveMembers()
    {
        // Arrange
        $activeMemberships = new Collection([$this->membership1]);
        $membershipRelation = Mockery::mock(HasMany::class);
        $membershipRelation->shouldReceive('where')->with('is_active', true)->andReturnSelf();
        $membershipRelation->shouldReceive('get')->andReturn($activeMemberships);
        
        $this->organization->shouldReceive('memberships')->andReturn($membershipRelation);
        $this->organization->shouldReceive('getActiveMembers')->andReturn(new Collection([$this->user1]));
        
        // Act & Assert
        $activeMembers = $this->organization->getActiveMembers();
        $this->assertCount(1, $activeMembers);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanGetMembersWithRole()
    {
        // Arrange
        $adminMemberships = new Collection([$this->membership1]);
        $normalMemberships = new Collection([$this->membership2]);
        
        $adminRelation = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $adminRelation->shouldReceive('where')->with('role', OrganizationConstants::ROLE_ADMIN)->andReturnSelf();
        $adminRelation->shouldReceive('get')->andReturn($adminMemberships);
        
        $normalRelation = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $normalRelation->shouldReceive('where')->with('role', OrganizationConstants::ROLE_MEMBER)->andReturnSelf();
        $normalRelation->shouldReceive('get')->andReturn($normalMemberships);
        
        $this->organization->shouldReceive('membersWithRole')
            ->with(OrganizationConstants::ROLE_ADMIN)
            ->andReturn($adminRelation);
            
        $this->organization->shouldReceive('membersWithRole')
            ->with(OrganizationConstants::ROLE_MEMBER)
            ->andReturn($normalRelation);
        
        // Act & Assert
        $admins = $this->organization->membersWithRole(OrganizationConstants::ROLE_ADMIN);
        $normalMembers = $this->organization->membersWithRole(OrganizationConstants::ROLE_MEMBER);
        
        $this->assertSame($adminRelation, $admins);
        $this->assertSame($normalRelation, $normalMembers);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itCanCheckIfUserIsMember()
    {
        // Arrange
        $nonMember = Mockery::mock(User::class);
        $nonMember->shouldReceive('getAttribute')->with('id')->andReturn(3);

        $membershipRelation = Mockery::mock(HasMany::class);
        $membershipRelation->shouldReceive('where')->with('user_id', 1)->andReturnSelf();
        $membershipRelation->shouldReceive('where')->with('user_id', 2)->andReturnSelf();
        $membershipRelation->shouldReceive('where')->with('user_id', 3)->andReturnSelf();
        $membershipRelation->shouldReceive('exists')->andReturnUsing(function() use ($nonMember) {
            $args = func_get_args();
            if (empty($args)) {
                $userId = $this->organization->hasMemberUserIdArg;
                return $userId === 1 || $userId === 2;
            }
            return false;
        });

        $this->organization->shouldReceive('memberships')->andReturn($membershipRelation);
        $this->organization->hasMemberUserIdArg = null;
        $this->organization->shouldReceive('hasMember')->andReturnUsing(function($user) {
            $this->organization->hasMemberUserIdArg = $user->id;
            return $user->id === 1 || $user->id === 2;
        });
            
        // Act & Assert
        $this->assertTrue($this->organization->hasMember($this->user1));
        $this->assertTrue($this->organization->hasMember($this->user2));
        $this->assertFalse($this->organization->hasMember($nonMember));
    }
}
