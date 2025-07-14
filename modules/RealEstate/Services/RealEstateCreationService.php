<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Services;

use Modules\RealEstate\Models\RealEstate;
use Modules\RealEstate\Support\RealEstateValidator;

class RealEstateCreationService
{
    public function createFromOrganization(
        int $organizationId,
        array $extensionData,
        int $userId,
    ): RealEstate {
        // Validar dados específicos
        $this->validateRealEstateData($extensionData);

        // Criar registro específico - apenas campos que existem na tabela real_estates
        return RealEstate::create([
            'organization_id' => $organizationId,
            'creci' => $extensionData['creci'],
            'state_registration' => $extensionData['state_registration'] ?? null,
        ]);
    }

    private function validateRealEstateData(array $data): void
    {
        RealEstateValidator::validate($data, true);
    }
}
