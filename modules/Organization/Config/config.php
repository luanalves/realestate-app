<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Config;

/**
 * Configurações e constantes para o módulo Organization.
 */
class OrganizationConfig
{
    /**
     * Nome do módulo.
     */
    public const MODULE_NAME = 'Organization';

    /**
     * Papéis padrões em organizações.
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_MEMBER = 'member';

    /**
     * Mapeamento dos papéis com suas descrições.
     */
    public const ROLES = [
        self::ROLE_ADMIN => 'Administrador da organização',
        self::ROLE_MANAGER => 'Gerente na organização',
        self::ROLE_MEMBER => 'Membro da organização',
    ];
}
