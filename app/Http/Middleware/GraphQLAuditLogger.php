<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */
declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Security\Services\AuditLoggerService;

class GraphQLAuditLogger
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        if ($request->is('graphql')) {
            $user = Auth::user();
            $query = $request->input('query');
            $variables = $request->input('variables');
            $headers = $request->headers->all();
            $operation = $this->getOperationName($query);
            $uuid = (string) Str::uuid();
            $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;

            $details = [
                'uuid' => $uuid,
                'variables' => $variables,
                'full_query' => $query,
                'headers' => $headers,
                'response' => [
                    'status' => $status,
                    'body' => method_exists($response, 'getContent') ? json_decode($response->getContent(), true) : null,
                ],
            ];

            AuditLoggerService::logRequest([
                'uuid' => $uuid,
                'user_id' => $user?->id,
                'email' => $user?->email,
                'operation' => $operation,
                'module' => null, // Pode ser extraído do contexto se necessário
                'ip' => $request->ip(),
                'status' => $status,
                'created_at' => now(),
            ], $operation, $status, $details);
        }

        return $response;
    }

    protected function getOperationName(?string $query): ?string
    {
        if (!$query) {
            return null;
        }
        if (preg_match('/(mutation|query)\\s+(\\w+)/', $query, $matches)) {
            return $matches[2] ?? null;
        }

        return null;
    }
}
