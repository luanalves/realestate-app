<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\UserManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', RolesSeeder::ROLE_SUPER_ADMIN)->value('id');

        if (! DB::table('users')->where('email', 'contato@thedevkitchen.com.br')->exists()) {
            DB::table('users')->insert([
                'name' => 'the dev kitchen',
                'email' => 'contato@thedevkitchen.com.br',
                'password' => Hash::make('senha123'),
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
