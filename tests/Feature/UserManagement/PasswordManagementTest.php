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
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PasswordManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test changing password with valid credentials.
     */
    public function testChangePasswordSuccess(): void
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('current_password'),
        ]);

        // Authenticate the user
        Passport::actingAs($user);

        // Execute the mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    changePassword(
                        current_password: "current_password",
                        new_password: "new_password123",
                        new_password_confirmation: "new_password123"
                    ) {
                        success
                        message
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'changePassword' => [
                        'success' => true,
                        'message' => 'Password has been changed successfully',
                    ],
                ],
            ]);

        // Assert the password was actually changed
        $updatedUser = User::find($user->id);
        $this->assertTrue(Hash::check('new_password123', $updatedUser->password));
    }

    /**
     * Test changing password with incorrect current password.
     */
    public function testChangePasswordWithIncorrectCurrentPassword(): void
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('correct_password'),
        ]);

        // Authenticate the user
        Passport::actingAs($user);

        // Execute the mutation with incorrect current password
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    changePassword(
                        current_password: "wrong_password",
                        new_password: "new_password123",
                        new_password_confirmation: "new_password123"
                    ) {
                        success
                        message
                    }
                }
            ',
        ]);

        // Assert the response indicates failure
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'changePassword' => [
                        'success' => false,
                        'message' => 'Your current password is incorrect.',
                    ],
                ],
            ]);

        // Assert password wasn't changed
        $updatedUser = User::find($user->id);
        $this->assertTrue(Hash::check('correct_password', $updatedUser->password));
    }

    /**
     * Test changing password with mismatched confirmation.
     */
    public function testChangePasswordWithMismatchedConfirmation(): void
    {
        // Create a user
        $user = User::factory()->create([
            'password' => Hash::make('current_password'),
        ]);

        // Authenticate the user
        Passport::actingAs($user);

        // Execute the mutation with mismatched password confirmation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    changePassword(
                        current_password: "current_password",
                        new_password: "new_password123",
                        new_password_confirmation: "different_password"
                    ) {
                        success
                        message
                    }
                }
            ',
        ]);

        // Assert the response indicates failure
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'changePassword' => [
                        'success' => false,
                    ],
                ],
            ]);

        // Assert password wasn't changed
        $updatedUser = User::find($user->id);
        $this->assertTrue(Hash::check('current_password', $updatedUser->password));
    }

    /**
     * Test request password reset with valid email.
     */
    public function testRequestPasswordResetWithValidEmail(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Execute the mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    requestPasswordReset(
                        email: "' . $user->email . '"
                    ) {
                        success
                        message
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'requestPasswordReset' => [
                        'success' => true,
                    ],
                ],
            ]);
    }

    /**
     * Test request password reset with invalid email.
     */
    public function testRequestPasswordResetWithInvalidEmail(): void
    {
        // Execute the mutation with non-existent email
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    requestPasswordReset(
                        email: "nonexistent@example.com"
                    ) {
                        success
                        message
                    }
                }
            ',
        ]);

        // Assert the response indicates failure
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'requestPasswordReset' => [
                        'success' => false,
                    ],
                ],
            ]);
    }

    /**
     * Test reset password with valid token.
     * 
     * Note: This test mocks the password broker to avoid
     * having to generate a real token.
     */
    public function testResetPasswordWithValidToken(): void
    {
        // Mock the Password facade
        $this->mockPasswordBroker();

        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Execute the mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    resetPassword(
                        email: "test@example.com",
                        token: "valid-token",
                        password: "new_password123",
                        password_confirmation: "new_password123"
                    ) {
                        success
                        message
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'resetPassword' => [
                        'success' => true,
                    ],
                ],
            ]);
    }

    /**
     * Mock the password broker for testing.
     */
    private function mockPasswordBroker(): void
    {
        // This is a simplified mock for testing purposes
        // In a real implementation, you'd use a more comprehensive mock
        $this->mock('Illuminate\Auth\Passwords\PasswordBroker', function ($mock) {
            $mock->shouldReceive('sendResetLink')
                ->andReturn(\Illuminate\Support\Facades\Password::RESET_LINK_SENT);
            
            $mock->shouldReceive('reset')
                ->andReturnUsing(function ($credentials, $callback) {
                    if ($credentials['token'] === 'valid-token') {
                        $user = User::where('email', $credentials['email'])->first();
                        if ($user) {
                            $callback($user, $credentials['password']);
                            return \Illuminate\Support\Facades\Password::PASSWORD_RESET;
                        }
                    }
                    return \Illuminate\Support\Facades\Password::INVALID_TOKEN;
                });
        });
    }
}
