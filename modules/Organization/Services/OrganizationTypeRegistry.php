<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Services;

use Modules\Organization\Contracts\OrganizationTypeRegistryContract;

/**
 * Serviço para registro dinâmico de tipos de organização
 */
class OrganizationTypeRegistry implements OrganizationTypeRegistryContract
{
    /**
     * Array que mapeia tipos para suas classes
     *
     * @var array<string, string>
     */
    protected array $types = [];

    /**
     * Registra um novo tipo de organização
     *
     * @param string $type Nome do tipo de organização
     * @param string $class Classe que implementa este tipo
     * @return void
     */
    public function registerType(string $type, string $class): void
    {
        $this->types[$type] = $class;
    }

    /**
     * Obtém a classe associada a um tipo de organização
     *
     * @param string $type Nome do tipo
     * @return string|null Classe associada ou null se não encontrada
     */
    public function getClass(string $type): ?string
    {
        return $this->types[$type] ?? null;
    }

    /**
     * Obtém todos os tipos registrados
     *
     * @return array<string, string> Array com tipo => classe
     */
    public function getAllTypes(): array
    {
        return $this->types;
    }

    /**
     * Verifica se um tipo está registrado
     *
     * @param string $type Nome do tipo
     * @return bool
     */
    public function hasType(string $type): bool
    {
        return isset($this->types[$type]);
    }
}
