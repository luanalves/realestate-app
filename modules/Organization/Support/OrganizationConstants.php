<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Support;

/**
 * Classe auxiliar contendo constantes e configurações para o módulo Organization
 */
class OrganizationConstants
{
    /**
     * Nome do módulo
     */
    public const MODULE_NAME = 'Organization';
    
    /**
     * Papéis padrões em organizações
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_MEMBER = 'member';
    
    /**
     * Mapeamento dos papéis com suas descrições
     */
    public const ROLES = [
        self::ROLE_ADMIN => 'Administrador da organização',
        self::ROLE_MANAGER => 'Gerente na organização', 
        self::ROLE_MEMBER => 'Membro da organização',
    ];
    
    /**
     * Tipos de organizações suportadas pelo sistema
     */
    public const ORGANIZATION_TYPE_REAL_ESTATE = 'RealEstate';
    
    /**
     * Mapeamento entre tipos de organização e as classes correspondentes
     */
    public const ORGANIZATION_TYPE_MAP = [
        self::ORGANIZATION_TYPE_REAL_ESTATE => \Modules\RealEstate\Models\RealEstate::class,
    ];
    
    /**
     * Tipos de endereços
     */
    public const ADDRESS_TYPE_HEADQUARTERS = 'headquarters';
    public const ADDRESS_TYPE_BRANCH = 'branch';
    
    /**
     * Mapeamento dos tipos de endereços com suas descrições
     */
    public const ADDRESS_TYPES = [
        self::ADDRESS_TYPE_HEADQUARTERS => 'Matriz',
        self::ADDRESS_TYPE_BRANCH => 'Filial',
    ];
}
