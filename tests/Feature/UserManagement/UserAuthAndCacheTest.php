<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Feature\UserManagement;

use Modules\UserManagement\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Modules\UserManagement\Models\Role;
use Modules\UserManagement\Services\UserService;
use Tests\TestCase;

class UserAuthAndCacheTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create([
            'id' => 1,
            'name' => RolesSeeder::ROLE_SUPER_ADMIN,
            'description' => 'Super Administrator'
        ]);
        
        Role::create([
            'id' => 2,
            'name' => RolesSeeder::ROLE_CLIENT,
            'description' => 'Regular Client'
        ]);
    }

    /**
     * Test that users can only update their own data unless they are admins.
     */
    public function testUserModificationPermissions(): void
    {
        // Create two users - a regular user and an admin
        $regularUser = User::factory()->create([
            'role_id' => 2, // client role
            'email' => 'regular@example.com'
        ]);
        
        $adminUser = User::factory()->create([
            'role_id' => 1, // admin role
            'email' => 'admin@example.com'
        ]);
        
        $anotherUser = User::factory()->create([
            'role_id' => 2, // client role
            'email' => 'another@example.com',
            'name' => 'Original Name'
        ]);
        
        // 1. Regular user can update their own data
        Passport::actingAs($regularUser);
        
        $response = $this->graphQL('
            mutation {
                updateProfile(name: "Updated Name") {
                    success
                    user {
                        name
                    }
                }
            }
        ');
        
        $response->assertJson([
            'data' => [
                'updateProfile' => [
                    'success' => true,
                    'user' => [
                        'name' => 'Updated Name'
                    ]
                ]
            ]
        ]);
        
        // 2. Regular user cannot update another user's data
        $response = $this->graphQL('
            mutation {
                updateUser(id: "'.$anotherUser->id.'", input: {
                    name: "Unauthorized Change"
                }) {
                    id
                    name
                }
            }
        ');
        
        $response->assertJsonPath('errors.0.message', 'You do not have permission to modify other users');
        
        // 3. Admin user can update any user's data
        Passport::actingAs($adminUser);
        
        $response = $this->graphQL('
            mutation {
                updateUser(id: "'.$anotherUser->id.'", input: {
                    name: "Admin Changed Name"
                }) {
                    id
                    name
                }
            }
        ');
        
        $response->assertJson([
            'data' => [
                'updateUser' => [
                    'id' => (string)$anotherUser->id,
                    'name' => 'Admin Changed Name'
                ]
            ]
        ]);
        
        // Verify the change was actually made in the database
        $this->assertDatabaseHas('users', [
            'id' => $anotherUser->id,
            'name' => 'Admin Changed Name'
        ]);
    }
    
    /**
     * Test that cache is invalidated after user updates.
     */
    public function testCacheInvalidationAfterUpdate(): void
    {
        // Create a user
        $user = User::factory()->create([
            'role_id' => 2,
            'name' => 'Initial Name'
        ]);
        
        $userService = app(UserService::class);
        
        // Mock the cache to ensure we can check invalidation
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($user);
        
        Cache::shouldReceive('forget')
            ->once()
            ->andReturn(true);
        
        // Get the user by ID to cache it
        $retrievedUser = $userService->getUserById($user->id);
        $this->assertEquals('Initial Name', $retrievedUser->name);
        
        // Update the user
        $user->name = 'Updated Name';
        $user->save();
        
        // This verifies that the Cache::forget method was called once
        // which happens in UserService::invalidateUserCache
    }
    
    /**
     * Test that password changes require proper authentication.
     */
    public function testPasswordChangeRequiresAuthorization(): void
    {
        // Create users
        $user = User::factory()->create([
            'role_id' => 2,
            'password' => Hash::make('old_password'),
            'email' => 'user@example.com'
        ]);
        
        $adminUser = User::factory()->create([
            'role_id' => 1,
            'email' => 'admin@example.com'
        ]);
        
        $anotherUser = User::factory()->create([
            'role_id' => 2,
            'email' => 'another@example.com',
            'password' => Hash::make('another_password')
        ]);
        
        // 1. User can change their own password with correct current password
        Passport::actingAs($user);
        
        $response = $this->graphQL('
            mutation {
                changePassword(
                    current_password: "old_password",
                    new_password: "new_secure_password",
                    new_password_confirmation: "new_secure_password"
                ) {
                    success
                    message
                }
            }
        ');
        
        $response->assertJson([
            'data' => [
                'changePassword' => [
                    'success' => true
                ]
            ]
        ]);
        
        // 2. Admin can change another user's password through the updateUser mutation
        Passport::actingAs($adminUser);
        
        $response = $this->graphQL('
            mutation {
                updateUser(id: "'.$anotherUser->id.'", input: {
                    password: "admin_set_password"
                }) {
                    id
                    email
                }
            }
        ');
        
        $response->assertJson([
            'data' => [
                'updateUser' => [
                    'id' => (string)$anotherUser->id,
                    'email' => 'another@example.com'
                ]
            ]
        ]);
        
        // Verify the password was changed (by logging in with it)
        $loginSuccessful = Hash::check('admin_set_password', User::find($anotherUser->id)->password);
        $this->assertTrue($loginSuccessful, 'Password was not updated correctly');
    }
}
