<?php

/**
 * @author      Lua    public function handle(Request $request, Closure $next): BaseResponse
 * {
 * // Generate unique identifier for this request
 * $uuid = (string) Str::uuid();
 *
 * // Extract request data
 * $requestData = $this->extractRequestData($request, $uuid);opyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Security\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Security\Models\LogDetail;
use Modules\Security\Models\SecurityLog;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class GraphQLLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response|\Illuminate\Http\RedirectResponse) $next
     *
     * @return Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, \Closure $next): BaseResponse
    {
        // Generate unique identifier for this request
        $uuid = (string) Str::uuid();

        // Extract request data
        $requestData = $this->extractRequestData($request, $uuid);

        // Continue with the request
        $response = $next($request);

        // Extract response data and log
        $this->logGraphQLRequest($requestData, $response, $uuid);

        return $response;
    }

    /**
     * Extract request data for logging.
     */
    private function extractRequestData(Request $request, string $uuid): array
    {
        $user = Auth::user();
        $graphqlData = $request->input();

        // Extract GraphQL operation information
        $operation = $this->extractGraphQLOperation($graphqlData);
        $variables = $graphqlData['variables'] ?? [];

        return [
            'uuid' => $uuid,
            'user_id' => $user?->id,
            'email' => $user?->email,
            'operation' => $operation,
            'module' => $this->extractModule($operation),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'variables' => $variables,
            'query' => $graphqlData['query'] ?? '',
            'timestamp' => now(),
        ];
    }

    /**
     * Extract GraphQL operation name from the request.
     */
    private function extractGraphQLOperation(array $graphqlData): string
    {
        $query = $graphqlData['query'] ?? '';
        $operationName = $graphqlData['operationName'] ?? null;

        if ($operationName) {
            return $operationName;
        }

        // Try to extract operation from query string
        if (preg_match('/(?:query|mutation|subscription)\s+(\w+)/', $query, $matches)) {
            return $matches[1];
        }

        // Try to extract field name from simple queries
        if (preg_match('/{\s*(\w+)/', $query, $matches)) {
            return $matches[1];
        }

        return 'unknown_operation';
    }

    /**
     * Extract module name based on operation.
     */
    private function extractModule(string $operation): string
    {
        // Map operations to modules based on naming conventions
        $moduleMap = [
            'user' => 'UserManagement',
            'property' => 'Properties',
            'preference' => 'UserPreferences',
            'security' => 'Security',
            'auth' => 'Authentication',
        ];

        foreach ($moduleMap as $keyword => $module) {
            if (Str::contains(strtolower($operation), $keyword)) {
                return $module;
            }
        }

        return 'Unknown';
    }

    /**
     * Sanitize headers to remove sensitive information.
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'cookie', 'x-api-key'];

        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['[REDACTED]'];
            }
        }

        return $headers;
    }

    /**
     * Log the GraphQL request to both PostgreSQL and MongoDB.
     */
    private function logGraphQLRequest(array $requestData, BaseResponse $response, string $uuid): void
    {
        try {
            $status = $this->determineStatus($response);

            // Log basic information to PostgreSQL
            $securityLog = SecurityLog::create([
                'uuid' => $uuid,
                'user_id' => $requestData['user_id'],
                'email' => $requestData['email'],
                'operation' => $requestData['operation'],
                'module' => $requestData['module'],
                'ip_address' => $requestData['ip'],
                'status' => $status,
            ]);

            // Log detailed information to MongoDB
            $this->logDetailedToMongoDB($securityLog->id, $requestData, $response);
        } catch (\Exception $e) {
            // Log error but don't break the request flow
            Log::error('GraphQL logging middleware error', [
                'error' => $e->getMessage(),
                'uuid' => $uuid,
                'operation' => $requestData['operation'] ?? 'unknown',
            ]);
        }
    }

    /**
     * Determine request status based on response.
     */
    private function determineStatus(BaseResponse $response): string
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            // Check if GraphQL response contains errors
            $content = $response->getContent();
            if ($content && Str::contains($content, '"errors":')) {
                return 'graphql_error';
            }

            return 'success';
        }

        if ($statusCode >= 400 && $statusCode < 500) {
            return 'client_error';
        }

        if ($statusCode >= 500) {
            return 'server_error';
        }

        return 'unknown';
    }

    /**
     * Log detailed information to MongoDB.
     */
    private function logDetailedToMongoDB(int $securityLogId, array $requestData, BaseResponse $response): void
    {
        $responseContent = $response->getContent();
        $responseData = null;

        if ($responseContent && Str::isJson($responseContent)) {
            $responseData = json_decode($responseContent, true);
        }

        LogDetail::create([
            'security_log_id' => $securityLogId,
            'details' => [
                'request' => [
                    'headers' => $requestData['headers'],
                    'variables' => $requestData['variables'],
                    'query' => $requestData['query'],
                    'user_agent' => $requestData['user_agent'],
                    'timestamp' => $requestData['timestamp']->toISOString(),
                ],
                'response' => [
                    'status_code' => $response->getStatusCode(),
                    'headers' => $response->headers->all(),
                    'data' => $responseData,
                    'size' => strlen($responseContent ?: ''),
                ],
                'execution' => [
                    'duration_ms' => $this->calculateExecutionTime($requestData['timestamp']),
                    'memory_peak' => memory_get_peak_usage(true),
                    'memory_usage' => memory_get_usage(true),
                ],
            ],
        ]);
    }

    /**
     * Calculate execution time in milliseconds.
     */
    private function calculateExecutionTime(\DateTime $startTime): float
    {
        $endTime = now();

        return $endTime->diffInMilliseconds($startTime);
    }
}
