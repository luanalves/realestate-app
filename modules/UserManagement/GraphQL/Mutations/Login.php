<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class Login
{
    public function __invoke($_, array $args)
    {
        $validator = Validator::make($args, [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $validator->validate();

        $http = Http::asForm()->post(config('app.url').'/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'username' => $args['email'],
            'password' => $args['password'],
            'scope' => '',
        ]);

        if (! $http->ok()) {
            abort(401, 'Credenciais inválidas');
        }

        $tokenData = $http->json();

        $user = User::where('email', $args['email'])->firstOrFail();

        return [
            'access_token' => $tokenData['access_token'],
            'token_type' => $tokenData['token_type'],
            'expires_in' => $tokenData['expires_in'],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
            ],
        ];
    }
}
