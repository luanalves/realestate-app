<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index']);
