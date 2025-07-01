<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Tests\Feature;

use Modules\UserManagement\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Models\RealEstateAddress;
use Tests\TestCase;

class RealEstateGraphQLTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an authorized user
        $this->user = User::factory()->create();

        // Authenticate with Passport
        Passport::actingAs($this->user);

        // Seed test data
        $this->seedTestData();
    }

    /**
     * Seed test data for real estate tests.
     */
    private function seedTestData(): void
    {
        // Create test real estate
        $realEstate = RealEstate::create([
            'name' => 'Test Real Estate',
            'fantasy_name' => 'Test Agency',
            'corporate_name' => 'Test Real Estate Corp',
            'cnpj' => '12345678901234',
            'email' => 'test@realestate.com',
            'description' => 'Test agency description',
            'phone' => '(11) 1234-5678',
            'website' => 'https://example.com',
            'creci' => 'CRECI-TEST',
            'state_registration' => '123456789',
            'legal_representative' => 'Test Representative',
            'active' => true,
        ]);

        // Create address for test real estate
        RealEstateAddress::create([
            'real_estate_id' => $realEstate->id,
            'type' => 'headquarters',
            'street' => 'Test Street',
            'number' => '123',
            'neighborhood' => 'Test Neighborhood',
            'city' => 'Test City',
            'state' => 'TS',
            'zip_code' => '12345678',
            'country' => 'Brasil',
            'active' => true,
        ]);
    }

    /**
     * Test listing real estates.
     */
    public function testQueryRealEstates(): void
    {
        // Execute GraphQL query
        $response = $this->postJson('/graphql', [
            'query' => '
                query {
                    realEstates(first: 10) {
                        data {
                            id
                            name
                            fantasy_name
                            corporate_name
                            cnpj
                            email
                            active
                        }
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonPath('data.realEstates.data.0.name', 'Test Real Estate')
            ->assertJsonPath('data.realEstates.data.0.fantasy_name', 'Test Agency')
            ->assertJsonPath('data.realEstates.data.0.cnpj', '12345678901234');
    }

    /**
     * Test getting a real estate by ID.
     */
    public function testQueryRealEstateById(): void
    {
        // Get the test real estate
        $realEstate = RealEstate::first();

        // Execute GraphQL query
        $response = $this->postJson('/graphql', [
            'query' => '
                query($id: ID!) {
                    realEstateById(id: $id) {
                        id
                        name
                        fantasy_name
                        corporate_name
                        cnpj
                        email
                        addresses {
                            type
                            street
                            city
                            state
                        }
                    }
                }
            ',
            'variables' => [
                'id' => $realEstate->id,
            ],
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonPath('data.realEstateById.name', 'Test Real Estate')
            ->assertJsonPath('data.realEstateById.addresses.0.type', 'headquarters')
            ->assertJsonPath('data.realEstateById.addresses.0.city', 'Test City');
    }

    /**
     * Test creating a real estate.
     */
    public function testCreateRealEstate(): void
    {
        // Execute GraphQL mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation {
                    createRealEstate(
                        input: {
                            name: "New Real Estate"
                            fantasy_name: "New Agency"
                            corporate_name: "New Real Estate Corp"
                            cnpj: "98765432101234"
                            email: "new@realestate.com"
                            description: "New agency description"
                            phone: "(21) 9876-5432"
                            website: "https://new-example.com"
                            creci: "CRECI-NEW"
                            state_registration: "987654321"
                            legal_representative: "New Representative"
                            active: true
                        }
                    ) {
                        id
                        name
                        fantasy_name
                        corporate_name
                        cnpj
                    }
                }
            ',
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonPath('data.createRealEstate.name', 'New Real Estate')
            ->assertJsonPath('data.createRealEstate.fantasy_name', 'New Agency')
            ->assertJsonPath('data.createRealEstate.cnpj', '98765432101234');

        // Verify record was created in database
        $this->assertDatabaseHas('real_estates', [
            'name' => 'New Real Estate',
            'cnpj' => '98765432101234',
        ]);
    }

    /**
     * Test updating a real estate.
     */
    public function testUpdateRealEstate(): void
    {
        // Get the test real estate
        $realEstate = RealEstate::first();

        // Execute GraphQL mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation($id: ID!) {
                    updateRealEstate(
                        id: $id
                        input: {
                            name: "Updated Real Estate"
                            fantasy_name: "Updated Agency"
                            phone: "(11) 9999-8888"
                        }
                    ) {
                        id
                        name
                        fantasy_name
                        phone
                    }
                }
            ',
            'variables' => [
                'id' => $realEstate->id,
            ],
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonPath('data.updateRealEstate.name', 'Updated Real Estate')
            ->assertJsonPath('data.updateRealEstate.fantasy_name', 'Updated Agency')
            ->assertJsonPath('data.updateRealEstate.phone', '(11) 9999-8888');

        // Verify record was updated in database
        $this->assertDatabaseHas('real_estates', [
            'id' => $realEstate->id,
            'name' => 'Updated Real Estate',
            'fantasy_name' => 'Updated Agency',
            'phone' => '(11) 9999-8888',
        ]);
    }

    /**
     * Test deleting a real estate.
     */
    public function testDeleteRealEstate(): void
    {
        // Get the test real estate
        $realEstate = RealEstate::first();

        // Execute GraphQL mutation
        $response = $this->postJson('/graphql', [
            'query' => '
                mutation($id: ID!) {
                    deleteRealEstate(id: $id) {
                        id
                        name
                    }
                }
            ',
            'variables' => [
                'id' => $realEstate->id,
            ],
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonPath('data.deleteRealEstate.id', (string) $realEstate->id);

        // Verify record was deleted from database
        $this->assertDatabaseMissing('real_estates', [
            'id' => $realEstate->id,
            'deleted_at' => null,
        ]);
    }
}
