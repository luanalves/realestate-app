<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Organization\Models\Organization;
use Modules\RealEstate\Models\RealEstate;
use Tests\TestCase;

class RealEstateTest extends TestCase
{
    use RefreshDatabase;

    /**
    /**
     * Tests the creation and relationship between Organization and RealEstate.
     */
     */
    public function testCreateRealEstate(): void
    {
        // 1. Create using resolver para simular o processo real
        $resolver = new \Modules\RealEstate\GraphQL\Mutations\CreateRealEstateResolver();

        $input = [
            'name' => 'Imobiliária Teste',
            'fantasyName' => 'Imob Teste',
            'cnpj' => '12345678901234',
            'description' => 'Descrição da imobiliária teste',
            'email' => 'contato@imobiliaria.example.com',
            'phone' => '(11) 99999-9999',
            'website' => 'https://imobiliaria.example.com',
            'active' => true,
            'creci' => 'CRECI-123456',
            'stateRegistration' => '123.456.789',
        ];

        // Act
        $realEstate = $resolver(null, ['input' => $input]);

        // 3. Buscar do banco de dados
        $fetchedRealEstate = RealEstate::with('organization')->find($realEstate->id);

        // 4. Verificar que as propriedades foram salvas corretamente
        $this->assertNotNull($fetchedRealEstate);
        $this->assertEquals($realEstate->id, $fetchedRealEstate->id);

        // 5. Verificar acesso às propriedades da organização base
        $this->assertEquals('Imobiliária Teste', $fetchedRealEstate->name);
        $this->assertEquals('Descrição da imobiliária teste', $fetchedRealEstate->description);
        $this->assertEquals('contato@imobiliaria.example.com', $fetchedRealEstate->email);
        $this->assertEquals('(11) 99999-9999', $fetchedRealEstate->phone);
        $this->assertEquals('https://imobiliaria.example.com', $fetchedRealEstate->website);
        $this->assertTrue($fetchedRealEstate->active);

        // 6. Verificar propriedades específicas da imobiliária
        $this->assertEquals('Imob Teste', $fetchedRealEstate->fantasy_name); // Vindo da organização
        $this->assertEquals('12345678901234', $fetchedRealEstate->cnpj); // Vindo da organização
        $this->assertEquals('CRECI-123456', $fetchedRealEstate->creci);
        $this->assertEquals('123.456.789', $fetchedRealEstate->state_registration);
    }

    /**
     * Testa a exclusão em cascata.
     */
    public function testCascadeDelete(): void
    {
        // 1. Criar usando o resolver
        $resolver = new \Modules\RealEstate\GraphQL\Mutations\CreateRealEstateResolver();

        $input = [
            'name' => 'Imobiliária Para Deletar',
            'cnpj' => '98765432109876',
            'email' => 'delete@imobiliaria.example.com',
            'active' => true,
        ];

        $realEstate = $resolver(null, ['input' => $input]);

        // 3. Verificar que ambos foram criados
        $this->assertDatabaseHas('organizations', ['id' => $realEstate->id]);
        $this->assertDatabaseHas('real_estates', ['id' => $realEstate->id]); 
        // 5. Verify that both were deleted (cascade delete)
        $this->assertDatabaseMissing('organizations', ['id' => $realEstate->id]);
        $this->assertDatabaseMissing('real_estates', ['id' => $realEstate->id]);
        $this->assertDatabaseMissing('real_estates', ['id' => $realEstate->id]);
    }
}
