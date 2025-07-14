<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Organization\Models\Organization;
use Modules\RealEstate\Models\RealEstate;

class RealEstateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar uma organização para a imobiliária
        $organization = Organization::create([
            'name' => 'Imobiliária Teste',
            'fantasy_name' => 'Imob Test',
            'cnpj' => '12345678901234',
            'description' => 'Imobiliária para testes',
            'email' => 'contato@imobteste.com.br',
            'phone' => '11999999999',
            'website' => 'https://imobteste.com.br',
            'active' => true,
        ]);

        // Criar a imobiliária vinculada à organização
        RealEstate::create([
            'organization_id' => $organization->id,
            'creci' => 'J-12345',
            'state_registration' => '123.456.789.000',
        ]);

        // Criar mais uma para teste
        $organization2 = Organization::create([
            'name' => 'Imobiliária ABC',
            'fantasy_name' => 'ABC Imóveis',
            'cnpj' => '98765432109876',
            'description' => 'Segunda imobiliária para testes',
            'email' => 'contato@abcimoveis.com.br',
            'phone' => '11988888888',
            'website' => 'https://abcimoveis.com.br',
            'active' => true,
        ]);

        // Criar a segunda imobiliária
        RealEstate::create([
            'organization_id' => $organization2->id,
            'creci' => 'J-54321',
            'state_registration' => '987.654.321.000',
        ]);

        $this->command->info('Imobiliárias de teste criadas com sucesso!');
    }
}
