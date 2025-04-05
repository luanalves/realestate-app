<?php

namespace Modules\UserManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'super_admin', 'description' => 'Administrador geral do sistema'],
            ['name' => 'real_estate_admin', 'description' => 'Administrador da imobiliária'],
            ['name' => 'real_estate_agent', 'description' => 'Funcionário da imobiliária'],
            ['name' => 'client', 'description' => 'Cliente da imobiliária'],
        ]);
    }
}
