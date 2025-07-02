<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Mutations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Services\UserService;

class Login
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handles user login via GraphQL mutation, authenticating credentials and returning an OAuth2 access token with user data.
     *
     * Validates the provided email and password, authenticates the user using OAuth2, and retrieves formatted user information for the response.
     *
     * @param mixed $_ Unused parameter, required by the GraphQL resolver signature.
     * @param array $args The input arguments containing 'email' and 'password' for authentication.
     * @return array An array containing the OAuth2 access token details and the authenticated user's formatted data.
     */
    public function __invoke($_, array $args)
    {
        // 1. Validate input
        $validator = Validator::make($args, [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $validator->validate();

        // 2. Authenticate with OAuth2
        $http = Http::asForm()->post(config('app.url').'/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'username' => $args['email'],
            'password' => $args['password'],
            'scope' => '',
        ]);

        if (!$http->ok()) {
            Log::warning('Login failed - Invalid credentials', ['email' => $args['email']]);
            abort(401, 'Credenciais inválidas');
        }

        $tokenData = $http->json();

        // 3. Get user data with cache-first strategy
        try {
            $user = $this->userService->getAuthenticatedUserData($args['email']);
            $formattedUser = $this->userService->formatUserForResponse($user);

            return [
                'access_token' => $tokenData['access_token'],
                'token_type' => $tokenData['token_type'],
                'expires_in' => $tokenData['expires_in'],
                'user' => $formattedUser,
            ];
        } catch (\Exception $e) {
            Log::error('Login failed - User data retrieval error', [
                'email' => $args['email'],
                'error' => $e->getMessage(),
            ]);

            // Token was created but user data failed - this shouldn't happen
            // but we handle it gracefully
            abort(500, 'Erro interno do servidor');
        }
    }
}
