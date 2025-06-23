<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\Organization\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Organization\Models\OrganizationMembership;
use Modules\RealEstate\Models\RealEstate;
use Modules\Organization\Support\OrganizationConstants;
use Tests\TestCase;

class OrganizationMembershipTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    
    /** @test */
    public function it_can_create_organization_membership()
    {
        // Arrange
        $user = User::factory()->create();
        $realEstate = RealEstate::factory()->create();
        
        // Act
        $membership = OrganizationMembership::create([
            'user_id' => $user->id,
            'organization_type' => get_class($realEstate),
            'organization_id' => $realEstate->id,
            'role' => OrganizationConstants::ROLE_ADMIN,
            'position' => 'Diretor',
            'is_active' => true,
            'joined_at' => now(),
        ]);
        
        // Assert
        $this->assertDatabaseHas('organization_memberships', [
            'id' => $membership->id,
            'user_id' => $user->id,
            'organization_type' => get_class($realEstate),
            'organization_id' => $realEstate->id,
            'role' => OrganizationConstants::ROLE_ADMIN,
        ]);
    }
    
    /** @test */
    public function it_belongs_to_user()
    {
        // Arrange
        $user = User::factory()->create();
        $realEstate = RealEstate::factory()->create();
        
        // Act
        $membership = OrganizationMembership::create([
            'user_id' => $user->id,
            'organization_type' => get_class($realEstate),
            'organization_id' => $realEstate->id,
            'role' => OrganizationConstants::ROLE_ADMIN,
        ]);
        
        // Assert
        $this->assertInstanceOf(User::class, $membership->user);
        $this->assertEquals($user->id, $membership->user->id);
    }
    
    /** @test */
    public function it_morphs_to_organization()
    {
        // Arrange
        $user = User::factory()->create();
        $realEstate = RealEstate::factory()->create();
        
        // Act
        $membership = OrganizationMembership::create([
            'user_id' => $user->id,
            'organization_type' => get_class($realEstate),
            'organization_id' => $realEstate->id,
            'role' => OrganizationConstants::ROLE_ADMIN,
        ]);
        
        // Assert
        $this->assertInstanceOf(RealEstate::class, $membership->organization);
        $this->assertEquals($realEstate->id, $membership->organization->id);
    }
    
    /** @test */
    public function it_can_be_soft_deleted()
    {
        // Arrange
        $user = User::factory()->create();
        $realEstate = RealEstate::factory()->create();
        
        $membership = OrganizationMembership::create([
            'user_id' => $user->id,
            'organization_type' => get_class($realEstate),
            'organization_id' => $realEstate->id,
            'role' => OrganizationConstants::ROLE_ADMIN,
        ]);
        
        // Act
        $membership->delete();
        
        // Assert
        $this->assertSoftDeleted('organization_memberships', [
            'id' => $membership->id
        ]);
        $this->assertDatabaseMissing('organization_memberships', [
            'id' => $membership->id,
            'deleted_at' => null
        ]);
    }
}
