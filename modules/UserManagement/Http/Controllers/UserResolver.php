<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Http\Controllers;

use Modules\UserManagement\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Http\Requests\CreateUserRequest;

class UserResolver
{
    public function create($_, array $args): User
    {
        $request = app(CreateUserRequest::class);
        $request->merge($args['input']);
        $request->validateResolved();

        return User::create([
            'name' => $args['input']['name'],
            'email' => $args['input']['email'],
            'password' => Hash::make($args['input']['password']),
            'role_id' => $args['input']['role_id'],
        ]);
    }
}
