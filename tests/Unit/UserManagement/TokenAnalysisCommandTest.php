<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests\Unit\UserManagement;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\UserManagement\Console\Commands\TokenAnalysisCommand;
use Tests\TestCase;

class TokenAnalysisCommandTest extends TestCase
{
    use WithFaker;

    /**
     * TokenAnalysisCommand instance
     */
    protected $command;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new TokenAnalysisCommand();
    }

    /**
     * Test handle method with invalid action.
     */
    public function testHandleWithInvalidAction(): void
    {
        // Act & Assert
        $this->artisan('oauth:tokens', ['action' => 'invalid'])
            ->expectsOutputToContain('Unknown action: invalid. Use: list, analyze, or cleanup')
            ->assertExitCode(0);
    }

    /**
     * Test command signature and description.
     */
    public function testCommandSignatureAndDescription(): void
    {
        // Assert
        $this->assertEquals('oauth:tokens', $this->command->getName());
        $this->assertEquals('Analyze OAuth token behavior and management', $this->command->getDescription());
    }

    /**
     * Test format duration method with different time units.
     */
    public function testFormatDuration(): void
    {
        // Arrange
        $command = new TokenAnalysisCommand();
        $reflectionClass = new \ReflectionClass($command);
        $formatDurationMethod = $reflectionClass->getMethod('formatDuration');
        $formatDurationMethod->setAccessible(true);

        // Act & Assert - Test different durations
        $this->assertEquals(
            '1 year',
            $formatDurationMethod->invoke($command, 31536000)
        );

        $this->assertEquals(
            '2 years',
            $formatDurationMethod->invoke($command, 63072000)
        );

        $this->assertEquals(
            '1 month',
            $formatDurationMethod->invoke($command, 2592000)
        );

        $this->assertEquals(
            '1 day',
            $formatDurationMethod->invoke($command, 86400)
        );

        $this->assertEquals(
            '1 hour',
            $formatDurationMethod->invoke($command, 3600)
        );

        $this->assertEquals(
            '30 minutes',
            $formatDurationMethod->invoke($command, 1800)
        );

        $this->assertEquals(
            '45 seconds',
            $formatDurationMethod->invoke($command, 45)
        );

        $this->assertEquals(
            '1 second',
            $formatDurationMethod->invoke($command, 1)
        );

        $this->assertEquals(
            '0 seconds',
            $formatDurationMethod->invoke($command, 0)
        );
    }

    /**
     * Test format duration method with edge cases.
     */
    public function testFormatDurationWithEdgeCases(): void
    {
        // Arrange
        $command = new TokenAnalysisCommand();
        $reflectionClass = new \ReflectionClass($command);
        $formatDurationMethod = $reflectionClass->getMethod('formatDuration');
        $formatDurationMethod->setAccessible(true);

        // Act & Assert - Test edge cases
        $this->assertEquals(
            '1 minute',
            $formatDurationMethod->invoke($command, 60)
        );

        $this->assertEquals(
            '59 seconds',
            $formatDurationMethod->invoke($command, 59)
        );

        $this->assertEquals(
            '23 hours',
            $formatDurationMethod->invoke($command, 82800) // 23 * 3600
        );

        $this->assertEquals(
            '29 days',
            $formatDurationMethod->invoke($command, 2505600) // 29 * 86400
        );
    }

    /**
     * Test command has required arguments and options.
     */
    public function testCommandHasRequiredArgumentsAndOptions(): void
    {
        // Arrange
        $command = new TokenAnalysisCommand();

        // Act - Get command definition
        $definition = $command->getDefinition();

        // Assert - Check required argument
        $this->assertTrue($definition->hasArgument('action'));
        $actionArgument = $definition->getArgument('action');
        $this->assertTrue($actionArgument->isRequired());
        $this->assertEquals('Action to perform (list|analyze|cleanup)', $actionArgument->getDescription());

        // Assert - Check optional arguments
        $this->assertTrue($definition->hasOption('user-id'));
        $this->assertTrue($definition->hasOption('show-expired'));

        $userIdOption = $definition->getOption('user-id');
        $this->assertEquals('Filter by specific user ID', $userIdOption->getDescription());

        $showExpiredOption = $definition->getOption('show-expired');
        $this->assertEquals('Include expired tokens', $showExpiredOption->getDescription());
    }

    /**
     * Test command handles match expressions properly.
     */
    public function testCommandHandleMethodStructure(): void
    {
        // Arrange
        $command = new TokenAnalysisCommand();
        $reflectionClass = new \ReflectionClass($command);

        // Act - Check if method exists
        $this->assertTrue($reflectionClass->hasMethod('handle'));
        $this->assertTrue($reflectionClass->hasMethod('listTokens'));
        $this->assertTrue($reflectionClass->hasMethod('analyzeTokenBehavior'));
        $this->assertTrue($reflectionClass->hasMethod('cleanupExpiredTokens'));
        $this->assertTrue($reflectionClass->hasMethod('formatDuration'));

        // Assert - Methods are private except handle
        $handleMethod = $reflectionClass->getMethod('handle');
        $this->assertTrue($handleMethod->isPublic());

        $listTokensMethod = $reflectionClass->getMethod('listTokens');
        $this->assertTrue($listTokensMethod->isPrivate());

        $analyzeMethod = $reflectionClass->getMethod('analyzeTokenBehavior');
        $this->assertTrue($analyzeMethod->isPrivate());

        $cleanupMethod = $reflectionClass->getMethod('cleanupExpiredTokens');
        $this->assertTrue($cleanupMethod->isPrivate());

        $formatMethod = $reflectionClass->getMethod('formatDuration');
        $this->assertTrue($formatMethod->isPrivate());
    }

    /**
     * Test command exists and is properly registered.
     */
    public function testCommandIsRegistered(): void
    {
        // Act & Assert - Test that the command exists and can be called
        $this->artisan('oauth:tokens', ['action' => 'invalid'])
            ->assertExitCode(0);
    }
}
