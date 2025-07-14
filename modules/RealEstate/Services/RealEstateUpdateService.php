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

class RealEstateUpdateService
{
    public function updateFromOrganization(
        int $realEstateId,
        array $extensionData,
        int $userId,
    ): RealEstate {
        // Validar dados específicos se necessário
        if (isset($extensionData['creci'])) {
            $this->validateRealEstateData($extensionData);
        }

        // Buscar a imobiliária
        $realEstate = RealEstate::findOrFail($realEstateId);

        // Atualizar apenas campos que foram fornecidos
        if (isset($extensionData['creci'])) {
            $realEstate->creci = $extensionData['creci'];
        }

        if (isset($extensionData['state_registration'])) {
            $realEstate->state_registration = $extensionData['state_registration'];
        }

        $realEstate->save();

        return $realEstate;
    }

    private function validateRealEstateData(array $data): void
    {
        RealEstateValidator::validate($data, false);
    }
}
