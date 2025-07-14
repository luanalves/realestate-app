<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Support;

/**
 * Constantes específicas do módulo RealEstate.
 */
class RealEstateConstants
{
    /**
     * Nome do módulo.
     */
    public const MODULE_NAME = 'RealEstate';

    /**
     * Tipo de organização para imobiliárias.
     */
    public const ORGANIZATION_TYPE = 'RealEstate';

    /**
     * Status de registro no CRECI.
     */
    public const CRECI_STATUS_ACTIVE = 'active';
    public const CRECI_STATUS_INACTIVE = 'inactive';
    public const CRECI_STATUS_SUSPENDED = 'suspended';

    /**
     * Mapeamento dos status de CRECI.
     */
    public const CRECI_STATUS = [
        self::CRECI_STATUS_ACTIVE => 'Ativo',
        self::CRECI_STATUS_INACTIVE => 'Inativo',
        self::CRECI_STATUS_SUSPENDED => 'Suspenso',
    ];

    /**
     * Estados brasileiros válidos para registro.
     */
    public const VALID_STATES = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
        'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
        'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
    ];
}
