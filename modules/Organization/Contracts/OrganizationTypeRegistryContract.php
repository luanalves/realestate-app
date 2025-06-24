<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\Organization\Contracts;

/**
 * Interface para registro de tipos de organização
 */
interface OrganizationTypeRegistryContract
{
    /**
     * Registra um novo tipo de organização
     *
     * @param string $type Nome do tipo de organização
     * @param string $class Classe que implementa este tipo
     * @return void
     */
    public function registerType(string $type, string $class): void;

    /**
     * Obtém a classe associada a um tipo de organização
     *
     * @param string $type Nome do tipo
     * @return string|null Classe associada ou null se não encontrada
     */
    public function getClass(string $type): ?string;

    /**
     * Obtém todos os tipos registrados
     *
     * @return array<string, string> Array com tipo => classe
     */
    public function getAllTypes(): array;

    /**
     * Verifica se um tipo está registrado
     *
     * @param string $type Nome do tipo
     * @return bool
     */
    public function hasType(string $type): bool;
}
