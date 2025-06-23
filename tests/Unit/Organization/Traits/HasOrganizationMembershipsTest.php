<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\Organization\Traits;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Organization\Models\OrganizationMembership;
use Modules\Organization\Support\OrganizationConstants;
use Modules\Organization\Traits\HasOrganizationMemberships;
use Modules\RealEstate\Models\RealEstate;
use Tests\TestCase;

class HasOrganizationMembershipsTest extends TestCase
{
    use RefreshDatabase;

    protected RealEstate $realEstate;
    protected User $user1;
    protected User $user2;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->realEstate = RealEstate::factory()->create();
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        // Certifique-se de que RealEstate usa o trait
        $this->assertTrue(in_array(
            HasOrganizationMemberships::class,
            class_uses_recursive(RealEstate::class)
        ));

        // Crie algumas associações de membros
        OrganizationMembership::create([
            'user_id' => $this->user1->id,
            'organization_type' => get_class($this->realEstate),
            'organization_id' => $this->realEstate->id,
            'role' => OrganizationConstants::ROLE_ADMIN,
            'is_active' => true,
        ]);

        OrganizationMembership::create([
            'user_id' => $this->user2->id,
            'organization_type' => get_class($this->realEstate),
            'organization_id' => $this->realEstate->id,
            'role' => OrganizationConstants::ROLE_MEMBER,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function itCanGetMembers()
    {
        // Act
        $members = $this->realEstate->members;

        // Assert
        $this->assertCount(2, $members);
        $this->assertEquals($this->user1->id, $members[0]->id);
        $this->assertEquals($this->user2->id, $members[1]->id);
    }

    /** @test */
    public function itCanGetActiveMembers()
    {
        // Act
        $activeMembers = $this->realEstate->activeMembers;

        // Assert
        $this->assertCount(1, $activeMembers);
        $this->assertEquals($this->user1->id, $activeMembers[0]->id);
    }

    /** @test */
    public function itCanGetMembersWithRole()
    {
        // Act
        $admins = $this->realEstate->membersWithRole(OrganizationConstants::ROLE_ADMIN);
        $normalMembers = $this->realEstate->membersWithRole(OrganizationConstants::ROLE_MEMBER);

        // Assert
        $this->assertCount(1, $admins->get());
        $this->assertEquals($this->user1->id, $admins->first()->id);

        $this->assertCount(1, $normalMembers->get());
        $this->assertEquals($this->user2->id, $normalMembers->first()->id);
    }

    /** @test */
    public function itCanCheckIfHasMember()
    {
        // Act & Assert
        $this->assertTrue($this->realEstate->hasMember($this->user1));
        $this->assertTrue($this->realEstate->hasMember($this->user2));

        $newUser = User::factory()->create();
        $this->assertFalse($this->realEstate->hasMember($newUser));
    }
}
