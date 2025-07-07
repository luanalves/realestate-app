<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Services;

use Modules\RealEstate\Models\RealEstate;

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
        // Implementar validações específicas
        if (empty($data['creci'])) {
            throw new \InvalidArgumentException('CRECI is required for real estate');
        }

        // Validar formato do CRECI oficial brasileiro
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

        // Outras validações específicas...
    }
}
