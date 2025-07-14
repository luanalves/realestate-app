<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\UserManagement;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Modules\UserManagement\GraphQL\Mutations\RequestPasswordReset;
use Modules\UserManagement\Models\User;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tests\TestCase;

class RequestPasswordResetTest extends TestCase
{
    protected RequestPasswordReset $resetMutation;
    protected $mockContext;
    protected $mockResolveInfo;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the mutation instance
        $this->resetMutation = new RequestPasswordReset();
        
        // Mock the GraphQL context with request
        $this->mockContext = Mockery::mock(GraphQLContext::class);
        $mockRequest = Mockery::mock(\Illuminate\Http\Request::class);
        $mockRequest->shouldReceive('ip')->andReturn('127.0.0.1');
        $this->mockContext->shouldReceive('request')->andReturn($mockRequest);
        
        // Mock the ResolveInfo (not used in our code but required by the method signature)
        $this->mockResolveInfo = Mockery::mock(ResolveInfo::class);
        
        // Clear the rate limiter for testing
        RateLimiter::clear('password-reset:test@example.com|127.0.0.1');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that validation fails for invalid email
     */
    public function testValidationFailsForInvalidEmail(): void
    {
        // Mock Validator to fail
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(true);
        
        Validator::shouldReceive('errors')
            ->once()
            ->andReturnSelf();
            
        Validator::shouldReceive('first')
            ->once()
            ->andReturn('Email is invalid');
        
        $result = $this->resetMutation->__invoke(
            null, 
            ['email' => 'invalid-email'], 
            $this->mockContext, 
            $this->mockResolveInfo
        );
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Email is invalid', $result['message']);
    }

    /**
     * Test that email sending works when within rate limits
     */
    public function testEmailSendingSucceedsWhenWithinRateLimits(): void
    {
        // Mock Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(false);
        
        // Mock Password facade
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);
        
        $result = $this->resetMutation->__invoke(
            null, 
            ['email' => 'test@example.com'], 
            $this->mockContext, 
            $this->mockResolveInfo
        );
        
        $this->assertTrue($result['success']);
    }

    /**
     * Test that rate limiting blocks excessive attempts
     */
    public function testRateLimitingBlocksExcessiveAttempts(): void
    {
        // Set up initial conditions for rate limiting
        $key = 'password-reset:test@example.com|127.0.0.1';
        
        // Pre-fill rate limiter with max attempts
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($key);
        }
        
        // Mock Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(false);
        
        // Password facade should not be called because rate limiting kicks in
        Password::shouldReceive('sendResetLink')
            ->never();
        
        $result = $this->resetMutation->__invoke(
            null, 
            ['email' => 'test@example.com'], 
            $this->mockContext, 
            $this->mockResolveInfo
        );
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Too many password reset attempts', $result['message']);
    }

    /**
     * Test that rate limiter increments correctly
     */
    public function testRateLimiterIncrements(): void
    {
        // Clear any existing rate limits
        $key = 'password-reset:test@example.com|127.0.0.1';
        RateLimiter::clear($key);
        
        // Mock Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(false);
        
        // Mock Password facade
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);
        
        $this->resetMutation->__invoke(
            null, 
            ['email' => 'test@example.com'], 
            $this->mockContext, 
            $this->mockResolveInfo
        );
        
        // Check that the rate limiter was incremented
        $this->assertEquals(1, RateLimiter::attempts($key));
    }

    /**
     * Test that different email addresses have separate rate limits
     */
    public function testDifferentEmailsHaveSeparateRateLimits(): void
    {
        // Set up rate limiting for the first email
        $key1 = 'password-reset:test1@example.com|127.0.0.1';
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($key1);
        }
        
        // Mock Validator for the second email (should pass)
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(false);
        
        // Password facade should be called for the second email
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);
        
        $result = $this->resetMutation->__invoke(
            null, 
            ['email' => 'test2@example.com'], 
            $this->mockContext, 
            $this->mockResolveInfo
        );
        
        $this->assertTrue($result['success']);
    }
}
