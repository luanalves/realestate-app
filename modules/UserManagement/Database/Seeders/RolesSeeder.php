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

class RolesSeeder extends Seeder
{
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_REAL_ESTATE_ADMIN = 'real_estate_admin';

    public const ROLE_REAL_ESTATE_AGENT = 'real_estate_agent';

    public const ROLE_CLIENT = 'client';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Administrador geral do sistema',
        self::ROLE_REAL_ESTATE_ADMIN => 'Administrador da imobiliária',
        self::ROLE_REAL_ESTATE_AGENT => 'Funcionário da imobiliária',
        self::ROLE_CLIENT => 'Cliente da imobiliária',
    ];

    public function run(): void
    {
        foreach (self::ROLES as $name => $description) {
            $exists = DB::table('roles')->where('name', $name)->exists();

            if (!$exists) {
                DB::table('roles')->insert([
                    'name' => $name,
                    'description' => $description,
                ]);
                $this->command->info("Role added: $name");
            } else {
                $this->command->info("Role already exists: $name");
            }
        }
    }
}
