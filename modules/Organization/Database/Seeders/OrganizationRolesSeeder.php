<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Organization\Support\OrganizationConstants;

class OrganizationRolesSeeder extends Seeder
{
    /**
     * Outputs a table of available organization roles and their descriptions to the console.
     *
     * This seeder does not insert data into the database; it serves as documentation by displaying the roles defined in the OrganizationConstants class.
     */
    public function run(): void
    {
        // Aqui deveríamos inserir os papéis em uma tabela de papéis de organização.
        // Como estamos usando strings para os papéis no momento, este seeder serve
        // principalmente como documentação dos papéis disponíveis definidos em OrganizationConstants.
        
        $this->command->info('Organization roles are defined as constants in OrganizationConstants class.');
        $this->command->table(
            ['Role', 'Description'], 
            collect(OrganizationConstants::ROLES)->map(fn ($description, $name) => [$name, $description])->toArray()
        );
    }
}
