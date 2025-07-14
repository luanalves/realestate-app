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
use Laravel\Passport\Passport;
use Modules\UserManagement\Models\Role;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the 'me' query returns the authenticated user.
     */
    public function testMeQueryReturnsAuthenticatedUser(): void
    {
        // Create roles
        $role = Role::factory()->create(['name' => 'client']);
        
        // Create a user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'role_id' => $role->id,
        ]);

        // Authenticate the user
        Passport::actingAs($user);

        // Execute the query
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    me {
                        id
                        name
                        email
                        role {
                            id
                            name
                        }
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'me' => [
                        'id' => (string) $user->id, // GraphQL returns IDs as strings
                        'name' => 'John Doe',
                        'email' => 'johndoe@example.com',
                        'role' => [
                            'id' => (string) $role->id,
                            'name' => 'client',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Test the 'me' query when not authenticated.
     */
    public function testMeQueryWhenNotAuthenticated(): void
    {
        // Execute the query without authentication
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    me {
                        id
                        name
                        email
                    }
                }
            ',
        ]);

        // Assert authentication error
        $response->assertStatus(200)
            ->assertJson([
                'errors' => [
                    [
                        'message' => 'You must be logged in to view your profile',
                    ]
                ]
            ]);
    }
    
    /**
     * Test updating the user profile.
     */
    public function testUpdateProfile(): void
    {
        // Create a role
        $role = Role::factory()->create(['name' => 'client']);
        
        // Create a user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'role_id' => $role->id,
        ]);

        // Authenticate the user
        Passport::actingAs($user);

        // Execute the mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    updateProfile(
                        name: "Jane Doe",
                        email: "janedoe@example.com"
                    ) {
                        success
                        message
                        user {
                            id
                            name
                            email
                        }
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'updateProfile' => [
                        'success' => true,
                        'message' => 'Profile updated successfully',
                        'user' => [
                            'id' => (string) $user->id,
                            'name' => 'Jane Doe',
                            'email' => 'janedoe@example.com',
                        ],
                    ],
                ],
            ]);

        // Assert the database was updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
        ]);
    }
    
    /**
     * Test updating user preferences.
     */
    public function testUpdatePreferences(): void
    {
        // Create a role
        $role = Role::factory()->create(['name' => 'client']);
        
        // Create a user
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'role_id' => $role->id,
            'preferences' => null,
        ]);

        // Authenticate the user
        Passport::actingAs($user);

        // Execute the mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    updatePreferences(
                        preferences: {
                            darkMode: true,
                            notifications: {
                                email: true,
                                push: false
                            },
                            language: "pt-BR"
                        }
                    ) {
                        success
                        message
                        preferences
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'updatePreferences' => [
                        'success' => true,
                        'message' => 'Preferences updated successfully',
                        'preferences' => [
                            'darkMode' => true,
                            'notifications' => [
                                'email' => true,
                                'push' => false,
                            ],
                            'language' => 'pt-BR',
                        ],
                    ],
                ],
            ]);

        // Refresh the user instance
        $user->refresh();

        // Assert the preferences were saved
        $this->assertEquals([
            'darkMode' => true,
            'notifications' => [
                'email' => true,
                'push' => false,
            ],
            'language' => 'pt-BR',
        ], $user->preferences);
    }
}
