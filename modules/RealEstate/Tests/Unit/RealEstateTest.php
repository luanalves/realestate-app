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
use Modules\Organization\Support\OrganizationConstants;
use Modules\RealEstate\Models\RealEstate;
use Tests\TestCase;

class RealEstateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação e relação entre Organization e RealEstate
     */
    public function testCreateRealEstate(): void
    {
        // 1. Criar uma organização base
        $organization = new Organization();
        $organization->name = 'Imobiliária Teste';
        $organization->fantasy_name = 'Imob Teste';
        $organization->cnpj = '12345678901234';
        $organization->description = 'Descrição da imobiliária teste';
        $organization->email = 'contato@imobiliaria.example.com';
        $organization->phone = '(11) 99999-9999';
        $organization->website = 'https://imobiliaria.example.com';
        $organization->active = true;
        $organization->organization_type = OrganizationConstants::ORGANIZATION_TYPE_REAL_ESTATE;
        $organization->save();

        // 2. Criar uma imobiliária associada
        $realEstate = new RealEstate();
        $realEstate->id = $organization->id;
        $realEstate->creci = 'CRECI-123456';
        $realEstate->state_registration = '123.456.789';
        $realEstate->save();

        // 3. Buscar do banco de dados
        $fetchedRealEstate = RealEstate::with('organization')->find($organization->id);

        // 4. Verificar que as propriedades foram salvas corretamente
        $this->assertNotNull($fetchedRealEstate);
        $this->assertEquals($organization->id, $fetchedRealEstate->id);
        
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
     * Testa a exclusão em cascata
     */
    public function testCascadeDelete(): void
    {
        // 1. Criar uma organização base
        $organization = new Organization();
        $organization->name = 'Imobiliária Para Deletar';
        $organization->cnpj = '98765432109876';
        $organization->email = 'delete@imobiliaria.example.com';
        $organization->organization_type = OrganizationConstants::ORGANIZATION_TYPE_REAL_ESTATE;
        $organization->save();

        // 2. Criar uma imobiliária associada
        $realEstate = new RealEstate();
        $realEstate->id = $organization->id;
        $realEstate->save();

        // 3. Verificar que ambos foram criados
        $this->assertDatabaseHas('organizations', ['id' => $organization->id]);
        $this->assertDatabaseHas('real_estates', ['id' => $organization->id]);
        
        // 4. Excluir a organização base
        $organization->delete();
        
        // 5. Verificar que ambos foram excluídos (soft delete na organização)
        $this->assertSoftDeleted('organizations', ['id' => $organization->id]);
        $this->assertDatabaseMissing('real_estates', ['id' => $organization->id]);
    }
}
