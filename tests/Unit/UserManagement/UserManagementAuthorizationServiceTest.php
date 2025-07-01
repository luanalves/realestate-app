<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\UserManagement;

use Modules\UserManagement\Models\User;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Modules\UserManagement\Database\Seeders\RolesSeeder;
use Modules\UserManagement\Models\Role;
use Modules\UserManagement\Services\UserManagementAuthorizationService;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Tests\TestCase;

class UserManagementAuthorizationServiceTest extends TestCase
{
    private UserManagementAuthorizationService $authService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new UserManagementAuthorizationService();
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /**
     * Test that users can modify their own data
     */
    public function testUserCanModifyOwnData(): void
    {
        // Create a mock user with ID 1
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        
        // Setup Auth facade to return the mock user
        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        
        // Call the method with the same user ID
        $result = $this->authService->authorizeUserModification(1);
        
        // Assert that the method returns the user object
        $this->assertSame($user, $result);
    }
    
    /**
     * Test that admin users can modify other users' data
     */
    public function testAdminCanModifyOtherUsersData(): void
    {
        // Create a mock role with admin privileges
        $role = Mockery::mock(Role::class);
        $role->shouldReceive('getAttribute')->with('name')->andReturn(RolesSeeder::ROLE_SUPER_ADMIN);
        
        // Create a mock admin user with ID 1 and admin role
        $adminUser = Mockery::mock(User::class);
        $adminUser->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $adminUser->shouldReceive('getAttribute')->with('role')->andReturn($role);
        
        // Setup Auth facade to return the admin user
        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($adminUser);
        
        // Call the method with a different user ID
        $result = $this->authService->authorizeUserModification(2);
        
        // Assert that the method returns the admin user object
        $this->assertSame($adminUser, $result);
    }
    
    /**
     * Test that regular users cannot modify other users' data
     */
    public function testRegularUserCannotModifyOtherUsersData(): void
    {
        // Create a mock role with non-admin privileges
        $role = Mockery::mock(Role::class);
        $role->shouldReceive('getAttribute')->with('name')->andReturn(RolesSeeder::ROLE_CLIENT);
        
        // Create a mock regular user with ID 1 and client role
        $regularUser = Mockery::mock(User::class);
        $regularUser->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $regularUser->shouldReceive('getAttribute')->with('role')->andReturn($role);
        
        // Setup Auth facade to return the regular user
        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($regularUser);
        
        // Expect an exception when a regular user tries to modify another user's data
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('You do not have permission to modify other users');
        
        // Call the method with a different user ID
        $this->authService->authorizeUserModification(2);
    }
    
    /**
     * Test that unauthenticated users cannot modify any user data
     */
    public function testUnauthenticatedUserCannotModifyData(): void
    {
        // Setup Auth facade to indicate no authenticated user
        Auth::shouldReceive('guard')->with('api')->andReturnSelf();
        Auth::shouldReceive('check')->andReturn(false);
        
        // Expect an exception for unauthenticated users
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('You need to be authenticated to modify user data');
        
        // Call the method with any user ID
        $this->authService->authorizeUserModification(1);
    }
}
