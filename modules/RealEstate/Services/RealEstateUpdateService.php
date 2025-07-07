<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Services;

use Modules\RealEstate\Models\RealEstate;

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
        // Validar formato do CRECI oficial brasileiro se fornecido
        if (!empty($data['creci'])) {
            // Formatos aceitos:
            // - CRECI/SP 12345-J (formato completo)
            // - CRECI/SP 12345-F (pessoa física)
            // - 12345-J (formato simplificado)
            // - J-12345 (formato alternativo - compatibilidade)
            $creci = trim($data['creci']);
            $validFormats = [
                '/^CRECI\/[A-Z]{2}\s+\d{4,6}-[FJ]$/',  // CRECI/SP 12345-J
                '/^\d{4,6}-[FJ]$/',                     // 12345-J
                '/^[FJ]-\d{4,6}$/',                     // J-12345 (compatibilidade)
            ];

            $isValid = false;
            foreach ($validFormats as $pattern) {
                if (preg_match($pattern, $creci)) {
                    $isValid = true;
                    break;
                }
            }

            if (!$isValid) {
                throw new \InvalidArgumentException('Invalid CRECI format. Expected formats: CRECI/SP 12345-J, 12345-J, or J-12345. Use F for pessoa física or J for pessoa jurídica.');
            }

            // Validar se é pessoa jurídica (J) para imobiliárias
            if (!str_contains($creci, 'J') && !str_contains($creci, '-J')) {
                throw new \InvalidArgumentException('CRECI for real estate companies must end with -J (pessoa jurídica). Example: CRECI/SP 12345-J or 12345-J');
            }
        }

        // Outras validações específicas podem ser adicionadas aqui...
    }
}
