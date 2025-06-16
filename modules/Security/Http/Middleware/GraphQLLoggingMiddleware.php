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
use Modules\UserManagement\Services\UserService;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class GraphQLLoggingMiddleware
{
    private UserService $userService;

    /**
     * Operations that should be excluded from security logging.
     * These are typically development/introspection queries that don't represent user actions.
     */
    private const EXCLUDED_OPERATIONS = [
        'IntrospectionQuery',
        '__schema',
        '__type',
        '__typename',
        '_service',
        '_entities',
    ];

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response|\Illuminate\Http\RedirectResponse) $next
     *
     * @return Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, \Closure $next): BaseResponse
    {
        // Continue with the request
        $response = $next($request);

        // Check if this operation should be logged
        if ($this->shouldLogOperation($request)) {
            // Generate unique identifier for this request
            $uuid = (string) Str::uuid();

            // Extract request data
            $requestData = $this->extractRequestData($request, $uuid);

            // Log the GraphQL request
            $this->logGraphQLRequest($requestData, $response, $uuid);
        }

        return $response;
    }

    /**
     * Determine if the GraphQL operation should be logged.
     */
    private function shouldLogOperation(Request $request): bool
    {
        $graphqlData = $request->input();
        $operation = $this->extractGraphQLOperation($graphqlData);

        // Don't log excluded operations
        if (in_array($operation, self::EXCLUDED_OPERATIONS, true)) {
            return false;
        }

        // Don't log operations that start with double underscore (GraphQL introspection)
        if (str_starts_with($operation, '__')) {
            return false;
        }

        // Check if the query contains only introspection fields
        $query = $graphqlData['query'] ?? '';
        if ($this->isIntrospectionQuery($query)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a query is purely introspection-based.
     */
    private function isIntrospectionQuery(string $query): bool
    {
        // Remove comments and whitespace for analysis
        $cleanQuery = preg_replace('/\s+/', ' ', $query);
        $cleanQuery = preg_replace('/#[^\r\n]*/', '', $cleanQuery);

        // Check if query contains only introspection fields
        $introspectionPattern = '/^[^{]*\{\s*(__schema|__type|__typename|_service|_entities)/i';

        return (bool) preg_match($introspectionPattern, $cleanQuery);
    }

    /**
     * Extract request data for logging.
     */
    private function extractRequestData(Request $request, string $uuid): array
    {
        $authData = $this->extractAuthData($request);
        $graphqlData = $request->input();

        // Extract GraphQL operation information
        $operation = $this->extractGraphQLOperation($graphqlData);
        $variables = $graphqlData['variables'] ?? [];

        return [
            'uuid' => $uuid,
            'user_id' => $authData['user_id'],
            'email' => $authData['email'],
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
     * Extract authentication data from JWT token.
     */
    private function extractAuthData(Request $request): array
    {
        $userId = null;
        $email = null;

        // First try to get user_id from JWT token
        $userId = $this->extractUserIdFromJWT($request);

        // If we have a user_id, try to get email from UserService (with cache)
        if ($userId) {
            try {
                $user = $this->userService->getUserById($userId);
                $email = $user->email;
            } catch (\Exception $e) {
                // Log error but continue - we still have user_id from JWT
                Log::warning('GraphQL Middleware: Failed to get user email from UserService', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Fallback to Auth::user() if JWT extraction fails
            $user = Auth::user();
            if ($user) {
                $userId = $user->id;
                $email = $user->email;
            }
        }

        return [
            'user_id' => $userId,
            'email' => $email,
        ];
    }

    /**
     * Extract user ID from JWT token.
     */
    private function extractUserIdFromJWT(Request $request): ?int
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7); // Remove 'Bearer ' prefix

        if (empty($token)) {
            return null;
        }

        try {
            // Split JWT token (header.payload.signature)
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                return null;
            }

            // Decode payload (second part)
            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));

            if (!$payload) {
                return null;
            }

            $decodedPayload = json_decode($payload, true);

            if (!$decodedPayload) {
                return null;
            }

            // Extract user_id from 'sub' claim (Passport default)
            $userId = isset($decodedPayload['sub']) ? (int) $decodedPayload['sub'] : null;

            return $userId;
        } catch (\Exception $e) {
            Log::warning('GraphQL Middleware: Error extracting user_id from JWT', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
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
