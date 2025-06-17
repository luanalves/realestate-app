<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\Services;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Security\Models\SecurityLog;

class SecurityLogService
{
    /**
     * Get filtered and paginated security logs.
     */
    public function getFilteredLogs(array $filters, array $orderBy, int $perPage, int $page): LengthAwarePaginator
    {
        $query = SecurityLog::with('user');

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply ordering
        $this->applyOrdering($query, $orderBy);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get security log statistics.
     */
    public function getStatistics(array $filters): array
    {
        $query = SecurityLog::query();
        $this->applyFilters($query, $filters);

        // Total requests
        $totalRequests = $query->count();

        // Unique users
        $uniqueUsers = $query->whereNotNull('user_id')->distinct('user_id')->count();

        // Success rate
        $successCount = $query->where('status', 'success')->count();
        $successRate = $totalRequests > 0 ? ($successCount / $totalRequests) * 100 : 0;

        // Top operations
        $topOperations = $this->getTopOperations($filters);

        // Top modules
        $topModules = $this->getTopModules($filters);

        // Requests by status
        $requestsByStatus = $this->getRequestsByStatus($filters);

        // Requests by hour (last 24 hours)
        $requestsByHour = $this->getRequestsByHour($filters);

        return [
            'total_requests' => $totalRequests,
            'unique_users' => $uniqueUsers,
            'success_rate' => round($successRate, 2),
            'top_operations' => $topOperations,
            'top_modules' => $topModules,
            'requests_by_status' => $requestsByStatus,
            'requests_by_hour' => $requestsByHour,
        ];
    }

    /**
     * Apply filters to query.
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'ILIKE', '%'.$filters['email'].'%');
        }

        if (!empty($filters['operation'])) {
            $query->where('operation', 'ILIKE', '%'.$filters['operation'].'%');
        }

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['ip_address'])) {
            $query->where('ip_address', 'ILIKE', '%'.$filters['ip_address'].'%');
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to']));
        }

        if (!empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($search) {
                $q->where('email', 'ILIKE', $search)
                  ->orWhere('operation', 'ILIKE', $search)
                  ->orWhere('module', 'ILIKE', $search)
                  ->orWhere('ip_address', 'ILIKE', $search);
            });
        }
    }

    /**
     * Apply ordering to query.
     */
    private function applyOrdering($query, array $orderBy): void
    {
        foreach ($orderBy as $order) {
            $column = $this->mapOrderColumn($order['column']);
            $direction = strtolower($order['order']) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($column, $direction);
        }
    }

    /**
     * Map GraphQL column names to database column names.
     */
    private function mapOrderColumn(string $column): string
    {
        $columnMap = [
            'ID' => 'id',
            'USER_ID' => 'user_id',
            'OPERATION' => 'operation',
            'MODULE' => 'module',
            'IP_ADDRESS' => 'ip_address',
            'STATUS' => 'status',
            'CREATED_AT' => 'created_at',
        ];

        return $columnMap[$column] ?? 'created_at';
    }

    /**
     * Get top operations with count and percentage.
     */
    private function getTopOperations(array $filters, int $limit = 10): array
    {
        $query = SecurityLog::query();
        $this->applyFilters($query, $filters);

        $total = $query->count();

        $operations = $query->select('operation', DB::raw('COUNT(*) as count'))
            ->groupBy('operation')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();

        return $operations->map(function ($operation) use ($total) {
            return [
                'operation' => $operation->operation,
                'count' => $operation->count,
                'percentage' => $total > 0 ? round(($operation->count / $total) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Get top modules with count and percentage.
     */
    private function getTopModules(array $filters, int $limit = 10): array
    {
        $query = SecurityLog::query();
        $this->applyFilters($query, $filters);

        $total = $query->count();

        $modules = $query->select('module', DB::raw('COUNT(*) as count'))
            ->whereNotNull('module')
            ->groupBy('module')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();

        return $modules->map(function ($module) use ($total) {
            return [
                'module' => $module->module,
                'count' => $module->count,
                'percentage' => $total > 0 ? round(($module->count / $total) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Get requests grouped by status.
     */
    private function getRequestsByStatus(array $filters): array
    {
        $query = SecurityLog::query();
        $this->applyFilters($query, $filters);

        $total = $query->count();

        $statuses = $query->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return $statuses->map(function ($status) use ($total) {
            return [
                'status' => $status->status,
                'count' => $status->count,
                'percentage' => $total > 0 ? round(($status->count / $total) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Get requests grouped by hour (last 24 hours).
     */
    private function getRequestsByHour(array $filters): array
    {
        $query = SecurityLog::query();
        $this->applyFilters($query, $filters);

        // If no date filter is specified, default to last 24 hours
        if (empty($filters['date_from']) && empty($filters['date_to'])) {
            $query->where('created_at', '>=', Carbon::now()->subHours(24));
        }

        $hourlyStats = $query->select(
            DB::raw('EXTRACT(HOUR FROM created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy(DB::raw('EXTRACT(HOUR FROM created_at)'))
            ->orderBy('hour')
            ->get();

        return $hourlyStats->map(function ($stat) {
            return [
                'hour' => (int) $stat->hour,
                'count' => $stat->count,
            ];
        })->toArray();
    }
}
