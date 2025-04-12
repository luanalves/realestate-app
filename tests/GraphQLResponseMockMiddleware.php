<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Tests;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GraphQLResponseMockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('graphql') && app()->environment('testing')) {
            // Extract operation name from the GraphQL query
            $query = $request->input('query');
            $operationName = $this->extractOperationName($query);
            
            // Check if we have a mocked response for this operation
            if ($operationName && app()->has("graphql_response_for_{$operationName}")) {
                $mockedResponse = app()->get("graphql_response_for_{$operationName}");
                return response()->json($mockedResponse);
            }
        }
        
        return $next($request);
    }
    
    /**
     * Extract the operation name from a GraphQL query.
     * 
     * @param string $query The GraphQL query
     * @return string|null The operation name or null if not found
     */
    protected function extractOperationName(string $query): ?string
    {
        // Simple extraction of the first operation name
        // This is a basic implementation that works for our test cases
        
        // Extract query operations (query and mutation)
        if (preg_match('/(?:query|mutation)\s*(?:\(\$.*?\))?\s*{\s*(\w+)/m', $query, $matches)) {
            return $matches[1];
        }
        
        // Extract direct field queries without query keyword
        if (preg_match('/{\s*(\w+)/m', $query, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}