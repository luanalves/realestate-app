<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Http\Requests\CreateUserRequest;

class UserResolver
{
    public function create(null $_, array $args): User
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
