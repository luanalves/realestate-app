<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TokenAnalysisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth:tokens 
                           {action : Action to perform (list|analyze|cleanup)}
                           {--user-id= : Filter by specific user ID}
                           {--show-expired : Include expired tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze OAuth token behavior and management';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $action = $this->argument('action');
        $userId = $this->option('user-id');
        $showExpired = $this->option('show-expired');

        match ($action) {
            'list' => $this->listTokens($userId, $showExpired),
            'analyze' => $this->analyzeTokenBehavior(),
            'cleanup' => $this->cleanupExpiredTokens(),
            default => $this->error("Unknown action: {$action}. Use: list, analyze, or cleanup"),
        };
    }

    /**
     * List OAuth tokens with details.
     */
    private function listTokens(?string $userId, bool $showExpired): void
    {
        $this->info('🔑 OAuth Access Tokens Analysis');
        $this->line('');

        $query = DB::table('oauth_access_tokens')
            ->join('users', 'oauth_access_tokens.user_id', '=', 'users.id')
            ->select([
                'oauth_access_tokens.id',
                'oauth_access_tokens.user_id',
                'users.name',
                'users.email',
                'oauth_access_tokens.created_at',
                'oauth_access_tokens.expires_at',
                'oauth_access_tokens.revoked',
            ]);

        if ($userId) {
            $query->where('oauth_access_tokens.user_id', $userId);
        }

        if (!$showExpired) {
            $query->where('oauth_access_tokens.expires_at', '>', now());
        }

        $tokens = $query->orderBy('oauth_access_tokens.created_at', 'desc')->get();

        if ($tokens->isEmpty()) {
            $this->warn('No tokens found with the specified criteria.');

            return;
        }

        $tableData = [];
        foreach ($tokens as $token) {
            $expiresAt = Carbon::parse($token->expires_at);
            $createdAt = Carbon::parse($token->created_at);

            $status = '🟢 Active';
            if ($token->revoked) {
                $status = '🔴 Revoked';
            } elseif ($expiresAt->isPast()) {
                $status = '🟡 Expired';
            }

            $tableData[] = [
                substr($token->id, 0, 8).'...',
                $token->user_id,
                $token->name,
                $token->email,
                $createdAt->format('Y-m-d H:i:s'),
                $expiresAt->format('Y-m-d H:i:s'),
                $expiresAt->diffForHumans(),
                $status,
            ];
        }

        $this->table([
            'Token ID',
            'User ID',
            'Name',
            'Email',
            'Created',
            'Expires',
            'Time to Expire',
            'Status',
        ], $tableData);

        $this->line('');
        $this->info('📊 Total tokens found: '.count($tableData));
    }

    /**
     * Analyze token behavior and patterns.
     */
    private function analyzeTokenBehavior(): void
    {
        $this->info('📈 OAuth Token Behavior Analysis');
        $this->line('');

        // Total tokens analysis
        $totalTokens = DB::table('oauth_access_tokens')->count();
        $activeTokens = DB::table('oauth_access_tokens')
            ->where('expires_at', '>', now())
            ->where('revoked', false)
            ->count();
        $expiredTokens = DB::table('oauth_access_tokens')
            ->where('expires_at', '<=', now())
            ->count();
        $revokedTokens = DB::table('oauth_access_tokens')
            ->where('revoked', true)
            ->count();

        $this->table(['Metric', 'Count', 'Percentage'], [
            ['Total Tokens', $totalTokens, '100%'],
            ['Active Tokens', $activeTokens, $totalTokens > 0 ? round(($activeTokens / $totalTokens) * 100, 1).'%' : '0%'],
            ['Expired Tokens', $expiredTokens, $totalTokens > 0 ? round(($expiredTokens / $totalTokens) * 100, 1).'%' : '0%'],
            ['Revoked Tokens', $revokedTokens, $totalTokens > 0 ? round(($revokedTokens / $totalTokens) * 100, 1).'%' : '0%'],
        ]);

        // Token expiration analysis
        $this->line('');
        $this->info('⏰ Token Expiration Configuration');

        $sampleToken = DB::table('oauth_access_tokens')
            ->orderBy('created_at', 'desc')
            ->first();
        if ($sampleToken) {
            $createdAt = Carbon::parse($sampleToken->created_at);
            $expiresAt = Carbon::parse($sampleToken->expires_at);
            $duration = abs($expiresAt->diffInSeconds($createdAt));

            $this->line('🕐 Token Lifetime: '.$this->formatDuration((float) $duration));
            $this->line('📅 Sample Token Created: '.$createdAt->format('Y-m-d H:i:s'));
            $this->line('📅 Sample Token Expires: '.$expiresAt->format('Y-m-d H:i:s'));
        }

        // User token analysis
        $this->line('');
        $this->info('👥 User Token Distribution');

        $userTokens = DB::table('oauth_access_tokens')
            ->join('users', 'oauth_access_tokens.user_id', '=', 'users.id')
            ->select('users.email', DB::raw('COUNT(*) as token_count'))
            ->groupBy('users.id', 'users.email')
            ->orderBy('token_count', 'desc')
            ->get();

        $userTableData = [];
        foreach ($userTokens as $userToken) {
            $userTableData[] = [
                $userToken->email,
                $userToken->token_count,
            ];
        }

        $this->table(['User Email', 'Total Tokens'], $userTableData);

        $this->line('');
        $this->comment('💡 Key Insights:');
        $this->comment('• Each login generates a NEW token (no session reuse)');
        $this->comment('• Multiple active tokens per user are normal');
        $this->comment('• Tokens expire automatically based on configuration');
        $this->comment('• Old tokens should be cleaned up periodically');
    }

    /**
     * Clean up expired tokens.
     */
    private function cleanupExpiredTokens(): void
    {
        $this->info('🧹 Cleaning up expired OAuth tokens');

        $expiredCount = DB::table('oauth_access_tokens')
            ->where('expires_at', '<=', now())
            ->count();

        if ($expiredCount === 0) {
            $this->info('✅ No expired tokens found. Nothing to clean up.');

            return;
        }

        if ($this->confirm("Found {$expiredCount} expired tokens. Do you want to delete them?")) {
            $deleted = DB::table('oauth_access_tokens')
                ->where('expires_at', '<=', now())
                ->delete();

            // Also clean up related refresh tokens
            $deletedRefresh = DB::table('oauth_refresh_tokens')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('oauth_access_tokens')
                        ->whereColumn('oauth_refresh_tokens.access_token_id', 'oauth_access_tokens.id');
                })
                ->delete();

            $this->info("✅ Deleted {$deleted} expired access tokens");
            $this->info("✅ Deleted {$deletedRefresh} orphaned refresh tokens");
        } else {
            $this->info('❌ Cleanup cancelled');
        }
    }

    /**
     * Format duration in human readable format.
     */
    private function formatDuration(float $seconds): string
    {
        $units = [
            'year' => 31536000,
            'month' => 2592000,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1,
        ];

        foreach ($units as $unit => $value) {
            if ($seconds >= $value) {
                $amount = (int) floor($seconds / $value);

                return $amount.' '.$unit.($amount > 1 ? 's' : '');
            }
        }

        return '0 seconds';
    }
}
