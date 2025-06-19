<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Models\RealEstateAddress;

class RealEstateSeeder extends Seeder
{
    // Address types
    public const ADDRESS_TYPE_HEADQUARTERS = 'headquarters';
    public const ADDRESS_TYPE_BRANCH = 'branch';

    /**
     * Constants for test CNPJ values.
     */
    public const TEST_CNPJ_1 = '12345678901234';
    public const TEST_CNPJ_2 = '98765432109876';
    public const TEST_CNPJ_3 = '45678912345678';
    public const TEST_CNPJ_4 = '78912345678912';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $this->seedRealEstates();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Error seeding real estate data: '.$e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw in development, but handle gracefully in production
            if (app()->environment('local', 'development', 'testing')) {
                throw $e;
            }

            if ($this->command) {
                $this->command->error('Failed to seed real estate data. See logs for details.');
            }
        }
    }

    /**
     * Seed real estate agencies and their addresses.
     */
    protected function seedRealEstates(): void
    {
        // If CNPJ already exists, don't try to create new records
        if (RealEstate::where('cnpj', self::TEST_CNPJ_1)->exists()) {
            if ($this->command) {
                $this->command->info('Real Estate data already exists. Skipping seed process.');
            }

            return;
        }

        // Sample Real Estate Agency 1 - São Paulo
        $realEstate1 = RealEstate::create([
            'name' => 'Premium Imóveis SP',
            'fantasy_name' => 'Premium Imóveis',
            'corporate_name' => 'Premium Imóveis Ltda',
            'cnpj' => self::TEST_CNPJ_1,
            'description' => 'Especializada em imóveis de alto padrão na região de São Paulo.',
            'email' => 'contato@premiumimoveis.com.br',
            'phone' => '(11) 3456-7890',
            'website' => 'https://www.premiumimoveis.com.br',
            'creci' => 'CRECI-SP 12345',
            'state_registration' => '123456789012',
            'legal_representative' => 'João Silva Santos',
            'active' => true,
        ]);

        // Headquarters address for Real Estate 1
        RealEstateAddress::create([
            'real_estate_id' => $realEstate1->id,
            'type' => self::ADDRESS_TYPE_HEADQUARTERS,
            'street' => 'Avenida Paulista',
            'number' => '1000',
            'complement' => 'Conj. 101',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310100',
            'country' => 'Brasil',
            'active' => true,
        ]);

        // Branch address for Real Estate 1
        RealEstateAddress::create([
            'real_estate_id' => $realEstate1->id,
            'type' => self::ADDRESS_TYPE_BRANCH,
            'street' => 'Rua Augusta',
            'number' => '500',
            'complement' => 'Loja 1',
            'neighborhood' => 'Consolação',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01305000',
            'country' => 'Brasil',
            'active' => true,
        ]);

        // Sample Real Estate Agency 2 - Rio de Janeiro
        $realEstate2 = RealEstate::create([
            'name' => 'Rio Imóveis Carioca',
            'fantasy_name' => 'Rio Imóveis',
            'corporate_name' => 'Rio Imóveis Carioca Ltda',
            'cnpj' => self::TEST_CNPJ_2,
            'description' => 'Há 20 anos no mercado imobiliário carioca.',
            'email' => 'info@rioimoveis.com.br',
            'phone' => '(21) 2123-4567',
            'website' => 'https://www.rioimoveis.com.br',
            'creci' => 'CRECI-RJ 67890',
            'state_registration' => '987654321098',
            'legal_representative' => 'Maria Oliveira Costa',
            'active' => true,
        ]);

        // Headquarters address for Real Estate 2
        RealEstateAddress::create([
            'real_estate_id' => $realEstate2->id,
            'type' => self::ADDRESS_TYPE_HEADQUARTERS,
            'street' => 'Avenida Atlântica',
            'number' => '2000',
            'complement' => 'Sala 201',
            'neighborhood' => 'Copacabana',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'zip_code' => '22021001',
            'country' => 'Brasil',
            'active' => true,
        ]);

        // Branch address for Real Estate 2
        RealEstateAddress::create([
            'real_estate_id' => $realEstate2->id,
            'type' => self::ADDRESS_TYPE_BRANCH,
            'street' => 'Rua Barata Ribeiro',
            'number' => '370',
            'complement' => 'Loja Térreo',
            'neighborhood' => 'Copacabana',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'zip_code' => '22040000',
            'country' => 'Brasil',
            'active' => true,
        ]);

        // Sample Real Estate Agency 3 (Inactive for testing)
        $realEstate3 = RealEstate::create([
            'name' => 'BH Negócios Imobiliários',
            'fantasy_name' => 'BH Imóveis',
            'corporate_name' => 'BH Negócios Imobiliários Ltda',
            'cnpj' => self::TEST_CNPJ_3,
            'description' => 'Especializada no mercado de Belo Horizonte.',
            'email' => 'contato@bhimoveis.com.br',
            'phone' => '(31) 3334-5678',
            'website' => 'https://www.bhimoveis.com.br',
            'creci' => 'CRECI-MG 11111',
            'state_registration' => '111222333444',
            'legal_representative' => 'Carlos Pereira Lima',
            'active' => false, // Inactive for testing
        ]);

        // Headquarters address for Real Estate 3
        RealEstateAddress::create([
            'real_estate_id' => $realEstate3->id,
            'type' => self::ADDRESS_TYPE_HEADQUARTERS,
            'street' => 'Avenida Afonso Pena',
            'number' => '3000',
            'complement' => 'Andar 5',
            'neighborhood' => 'Centro',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
            'zip_code' => '30130009',
            'country' => 'Brasil',
            'active' => true,
        ]);

        // Sample Real Estate Agency 4 - Curitiba
        $realEstate4 = RealEstate::create([
            'name' => 'Curitiba Imobiliária Elite',
            'fantasy_name' => 'Elite Imóveis',
            'corporate_name' => 'Elite Negócios Imobiliários Ltda',
            'cnpj' => self::TEST_CNPJ_4,
            'description' => 'Imóveis de luxo e investimentos em Curitiba e região metropolitana.',
            'email' => 'contato@eliteimoveis.com.br',
            'phone' => '(41) 3040-9876',
            'website' => 'https://www.eliteimoveis.com.br',
            'creci' => 'CRECI-PR 54321',
            'state_registration' => '543210987654',
            'legal_representative' => 'Ana Paula Machado',
            'active' => true,
        ]);

        // Headquarters address for Real Estate 4
        RealEstateAddress::create([
            'real_estate_id' => $realEstate4->id,
            'type' => self::ADDRESS_TYPE_HEADQUARTERS,
            'street' => 'Rua XV de Novembro',
            'number' => '1500',
            'complement' => 'Sala 1010',
            'neighborhood' => 'Centro',
            'city' => 'Curitiba',
            'state' => 'PR',
            'zip_code' => '80020310',
            'country' => 'Brasil',
            'active' => true,
        ]);

        // Branch address for Real Estate 4
        RealEstateAddress::create([
            'real_estate_id' => $realEstate4->id,
            'type' => self::ADDRESS_TYPE_BRANCH,
            'street' => 'Avenida Batel',
            'number' => '950',
            'complement' => 'Loja 05',
            'neighborhood' => 'Batel',
            'city' => 'Curitiba',
            'state' => 'PR',
            'zip_code' => '80420090',
            'country' => 'Brasil',
            'active' => true,
        ]);

        // Check if running in console to display info message
        if ($this->command) {
            $this->command->info('Real Estate agencies and addresses seeded successfully!');
        }
    }
}
