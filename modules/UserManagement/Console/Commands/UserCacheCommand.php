<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Console\Commands;

use Illuminate\Console\Command;
use Modules\UserManagement\Factories\UserRepositoryFactory;
use Modules\UserManagement\Services\UserService;

class UserCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:cache 
                           {action : Action to perform (info|clear|test)}
                           {--user-id= : Specific user ID for cache operations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage user cache operations';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $action = $this->argument('action');
        $userId = $this->option('user-id');

        match ($action) {
            'info' => $this->showCacheInfo(),
            'clear' => $this->clearCache($userId),
            'test' => $this->testCache(),
            default => $this->error("Unknown action: {$action}. Use: info, clear, or test")
        };
    }

    /**
     * Show cache configuration and status information.
     */
    private function showCacheInfo(): void
    {
        $this->info('🔍 User Cache Information');
        $this->line('');

        $cacheInfo = UserRepositoryFactory::getCacheInfo();
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['Default Cache Store', $cacheInfo['default_cache_store']],
                ['Cache Available', $cacheInfo['is_available'] ? '✅ Yes' : '❌ No'],
                ['Redis Connection', $cacheInfo['redis_connection']],
                ['Cache Prefix', $cacheInfo['cache_prefix']],
            ]
        );

        $userService = app(UserService::class);
        $debugInfo = $userService->getDebugInfo();
        
        $this->line('');
        $this->info('🏭 Current Repository Configuration');
        $this->line("Repository Class: {$debugInfo['repository_class']}");
        $this->line("Timestamp: {$debugInfo['timestamp']}");
    }

    /**
     * Clear user cache.
     */
    private function clearCache(?string $userId): void
    {
        $userService = app(UserService::class);

        if ($userId) {
            $this->info("🧹 Clearing cache for user ID: {$userId}");
            $userService->invalidateUserCache((int) $userId);
            $this->info('✅ User cache cleared successfully');
        } else {
            $this->info('🧹 Clearing all user caches');
            $userService->clearAllUserCache();
            $this->info('✅ All user caches cleared successfully');
        }
    }

    /**
     * Test cache functionality.
     */
    private function testCache(): void
    {
        $this->info('🧪 Testing cache functionality');
        $this->line('');

        try {
            // Test factory creation
            $this->line('Testing factory creation...');
            $repository = UserRepositoryFactory::create();
            $this->info("✅ Factory created: " . get_class($repository));

            // Test service creation
            $this->line('Testing service creation...');
            $userService = app(UserService::class);
            $this->info("✅ Service created: " . get_class($userService));

            // Test cache info
            $this->line('Testing cache info...');
            $cacheInfo = UserRepositoryFactory::getCacheInfo();
            $this->info("✅ Cache available: " . ($cacheInfo['is_available'] ? 'Yes' : 'No'));

            $this->line('');
            $this->info('🎉 All tests passed successfully!');

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->line('');
            $this->warn('Please check your cache configuration and try again.');
        }
    }
}
