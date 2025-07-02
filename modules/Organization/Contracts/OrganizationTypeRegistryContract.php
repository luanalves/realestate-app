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
 * Registers a new organization type with its associated class.
 *
 * @param string $type The name of the organization type.
 * @param string $class The class name that implements this organization type.
 */
    public function registerType(string $type, string $class): void;

    /**
 * Retrieves the class name associated with the specified organization type.
 *
 * @param string $type The organization type name.
 * @return string|null The class name if the type is registered, or null if not found.
 */
    public function getClass(string $type): ?string;

    /**
 * Returns all registered organization types and their associated classes.
 *
 * @return array<string, string> An associative array mapping type names to class names.
 */
    public function getAllTypes(): array;

    /**
 * Determines whether a given organization type is registered.
 *
 * @param string $type The name of the organization type to check.
 * @return bool True if the type is registered, false otherwise.
 */
    public function hasType(string $type): bool;
}
